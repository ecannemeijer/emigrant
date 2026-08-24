<?php

namespace App\Controllers;

use App\Libraries\CalendarExport;
use App\Models\AppointmentModel;

class Appointment extends BaseController
{
    public function save()
    {
        $userId = (int) session()->get('userId');
        $model = new AppointmentModel();
        $id = (int) $this->request->getPost('id');
        $data = $this->fromPost($userId);

        if ($data['title'] === '') {
            return redirect()->to('/renovation/planning')->with('error', 'Vul een titel in voor de afspraak.');
        }

        if ($id > 0) {
            $existing = $model->forUserById($userId, $id);
            if ($existing) {
                $model->update($id, $data);
            }
        } else {
            $model->insert($data);
            $id = (int) $model->getInsertID();
        }

        return redirect()->to('/renovation/planning')->with('success', 'Afspraak opgeslagen.');
    }

    public function delete($id)
    {
        $userId = (int) session()->get('userId');
        $model = new AppointmentModel();
        $apt = $model->forUserById($userId, (int) $id);
        if ($apt) {
            $model->delete((int) $id);
        }

        return redirect()->to('/renovation/planning')->with('success', 'Afspraak verwijderd.');
    }

    public function google($id)
    {
        $userId = (int) session()->get('userId');
        $apt = (new AppointmentModel())->forUserById($userId, (int) $id);
        if (!$apt) {
            return redirect()->to('/renovation/planning')->with('error', 'Afspraak niet gevonden.');
        }

        $url = CalendarExport::googleUrl($apt);

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=utf-8')
            ->setBody(view('renovation/google_open', ['url' => $url]));
    }

    public function ics($id)
    {
        $userId = (int) session()->get('userId');
        $apt = (new AppointmentModel())->forUserById($userId, (int) $id);
        if (!$apt) {
            return redirect()->to('/renovation/planning')->with('error', 'Afspraak niet gevonden.');
        }

        $filename = 'afspraak-' . (int) $id . '.ics';
        return $this->response
            ->setHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(CalendarExport::ics($apt));
    }

    private function fromPost(int $userId): array
    {
        $title = trim((string) $this->request->getPost('title'));
        $allDay = (bool) $this->request->getPost('all_day');
        $date = trim((string) $this->request->getPost('date'));
        $startTime = trim((string) $this->request->getPost('start_time')) ?: '09:00';
        $endTime = trim((string) $this->request->getPost('end_time')) ?: '10:00';

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime = '09:00';
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime = '10:00';
        }

        $tz = new \DateTimeZone(CalendarExport::TZ);
        if ($allDay) {
            $start = new \DateTime($date . ' 00:00:00', $tz);
            $end = (clone $start)->modify('+1 day');
        } else {
            $start = new \DateTime($date . ' ' . $startTime . ':00', $tz);
            $end = new \DateTime($date . ' ' . $endTime . ':00', $tz);
            if ($end <= $start) {
                $end = (clone $start)->modify('+1 hour');
            }
        }

        return [
            'user_id' => $userId,
            'title' => $title,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'location' => trim((string) $this->request->getPost('location')) ?: null,
            'starts_at' => $start->format('Y-m-d H:i:s'),
            'ends_at' => $end->format('Y-m-d H:i:s'),
            'all_day' => $allDay ? 1 : 0,
        ];
    }
}
