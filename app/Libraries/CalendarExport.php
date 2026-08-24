<?php

namespace App\Libraries;

class CalendarExport
{
    public const TZ = 'Europe/Amsterdam';

    public static function googleUrl(array $apt): string
    {
        $title = (string) ($apt['title'] ?? 'Afspraak');
        $details = (string) ($apt['description'] ?? '');
        $location = (string) ($apt['location'] ?? '');
        $allDay = !empty($apt['all_day']);

        $start = new \DateTime((string) $apt['starts_at'], new \DateTimeZone(self::TZ));
        $end = new \DateTime((string) $apt['ends_at'], new \DateTimeZone(self::TZ));
        if ($end <= $start) {
            $end = (clone $start)->modify($allDay ? '+1 day' : '+1 hour');
        }

        if ($allDay) {
            $endExclusive = clone $end;
            if ($endExclusive->format('Y-m-d') === $start->format('Y-m-d')) {
                $endExclusive->modify('+1 day');
            }
            $dates = $start->format('Ymd') . '/' . $endExclusive->format('Ymd');
        } else {
            $dates = $start->format('Ymd\THis') . '/' . $end->format('Ymd\THis');
        }

        $query = http_build_query([
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => $dates,
            'details' => $details,
            'location' => $location,
            'ctz' => self::TZ,
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://calendar.google.com/calendar/render?' . $query;
    }

    public static function ics(array $apt): string
    {
        $title = self::icsEscape((string) ($apt['title'] ?? 'Afspraak'));
        $details = self::icsEscape((string) ($apt['description'] ?? ''));
        $location = self::icsEscape((string) ($apt['location'] ?? ''));
        $allDay = !empty($apt['all_day']);
        $uid = 'appointment-' . (int) ($apt['id'] ?? 0) . '@emigrant';

        $start = new \DateTime((string) $apt['starts_at'], new \DateTimeZone(self::TZ));
        $end = new \DateTime((string) $apt['ends_at'], new \DateTimeZone(self::TZ));
        if ($end <= $start) {
            $end = (clone $start)->modify($allDay ? '+1 day' : '+1 hour');
        }
        $stamp = new \DateTime('now', new \DateTimeZone('UTC'));

        if ($allDay) {
            if ($end->format('Y-m-d') === $start->format('Y-m-d')) {
                $end = (clone $start)->modify('+1 day');
            }
            $dtStart = 'DTSTART;VALUE=DATE:' . $start->format('Ymd');
            $dtEnd = 'DTEND;VALUE=DATE:' . $end->format('Ymd');
        } else {
            $dtStart = 'DTSTART;TZID=' . self::TZ . ':' . $start->format('Ymd\THis');
            $dtEnd = 'DTEND;TZID=' . self::TZ . ':' . $end->format('Ymd\THis');
        }

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//EmigreerItalia//Planning//NL',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $stamp->format('Ymd\THis\Z'),
            $dtStart,
            $dtEnd,
            'SUMMARY:' . $title,
            'DESCRIPTION:' . $details,
            'LOCATION:' . $location,
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    private static function icsEscape(string $value): string
    {
        return str_replace(["\\", ";", ",", "\n", "\r"], ["\\\\", "\;", "\,", '\n', ''], $value);
    }
}
