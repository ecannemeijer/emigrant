<?= $this->extend('layout') ?>
<?= $this->section('styles') ?>
<style>
#renoCalendar .cal-month-head,
#renoCalendar .cal-week-head,
#renoCalendar .cal-month,
#renoCalendar .cal-week,
#renoCalendar .cal-mini-grid {
    display: grid !important;
    grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
    gap: 6px;
}
#renoCalendar .cal-year {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px;
}
#renoCalendar .cal-time-head,
#renoCalendar .cal-allday,
#renoCalendar .cal-time-row {
    display: grid !important;
    grid-template-columns: 72px repeat(7, minmax(0, 1fr)) !important;
    gap: 4px;
}
#renoCalendar .cal-allday-one,
#renoCalendar .cal-time-grid-one .cal-time-row {
    grid-template-columns: 72px minmax(0, 1fr) !important;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$rooms = $rooms ?? [];
$statuses = $statuses ?? [];
?>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <p class="reno-kicker mb-1">Planning</p>
        <h1 class="mb-2">Kalender</h1>
        <p class="mb-0 text-muted">Overzicht van verbouwposten op dag, week, maand en jaar. Klik op een post om die te bewerken, of op een lege dag om een nieuwe post te plannen.</p>
    </div>
    <a class="btn btn-outline-secondary" href="/renovation"><i class="bi bi-hammer"></i> Terug naar verbouwen</a>
</div>

<div class="bc-page">
    <div class="bc-header">
        <div class="bc-views" role="group" aria-label="Weergave">
            <button type="button" class="bc-view reno-cal-view" data-view="agenda" title="Agenda"><i class="bi bi-list-ul"></i> <span>Agenda</span></button>
            <button type="button" class="bc-view reno-cal-view" data-view="year" title="Jaar"><i class="bi bi-grid"></i> <span>Jaar</span></button>
            <button type="button" class="bc-view reno-cal-view active" data-view="month" title="Maand"><i class="bi bi-calendar3"></i> <span>Maand</span></button>
            <button type="button" class="bc-view reno-cal-view" data-view="week" title="Week"><i class="bi bi-layout-three-columns"></i> <span>Week</span></button>
            <button type="button" class="bc-view reno-cal-view" data-view="day" title="Dag"><i class="bi bi-square"></i> <span>Dag</span></button>
        </div>
        <div class="bc-nav">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="renoCalPrev" aria-label="Vorige"><i class="bi bi-chevron-left"></i></button>
            <h2 class="h5 mb-0" id="renoCalTitle">Kalender</h2>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="renoCalNext" aria-label="Volgende"><i class="bi bi-chevron-right"></i></button>
            <button type="button" class="btn btn-sm btn-dark" id="renoCalToday">Vandaag</button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" id="renoCalAdd" <?= empty($rooms) ? 'disabled' : '' ?>>
            <i class="bi bi-plus"></i> Nieuwe post
        </button>
    </div>
    <div class="bc-legend">
        <span><i class="cal-dot planned"></i> Gepland</span>
        <span><i class="cal-dot quoted"></i> Offerte</span>
        <span><i class="cal-dot in_progress"></i> Bezig</span>
        <span><i class="cal-dot done"></i> Klaar</span>
        <span><i class="cal-dot skipped"></i> Vervalt</span>
    </div>
    <div id="renoCalendar" class="reno-calendar bc-body"></div>
    <div id="renoCalUnscheduled" class="reno-cal-unscheduled"></div>
</div>

<?php if (empty($rooms)): ?>
    <p class="small text-muted mt-3">Maak eerst een categorie op de <a href="/renovation">verbouwpagina</a> voordat je posten in de kalender zet.</p>
<?php endif; ?>

<?= view('renovation/partials/category_modal', ['returnTo' => 'planning']) ?>
<?= view('renovation/partials/item_modal', ['returnTo' => 'planning', 'rooms' => $rooms, 'statuses' => $statuses, 'priorities' => $priorities ?? []]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
window.renoCalItems = <?= json_encode($calendarItems ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
window.renoCalCats = <?= json_encode($categories ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<script src="/js/renovation-form.js?v=<?= @filemtime(FCPATH . 'js/renovation-form.js') ?: time() ?>"></script>
<script src="/js/renovation-calendar.js?v=<?= @filemtime(FCPATH . 'js/renovation-calendar.js') ?: time() ?>"></script>
<?= $this->endSection() ?>
