<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/renovation/appointment" method="post" id="appointmentForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="apt_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalTitle">Nieuwe afspraak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="apt_title">Titel</label>
                        <input type="text" class="form-control" name="title" id="apt_title" required maxlength="180" placeholder="Bijv. Geometra, notaris, aannemer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="apt_date">Datum</label>
                        <input type="date" class="form-control" name="date" id="apt_date" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="all_day" id="apt_all_day" value="1">
                        <label class="form-check-label" for="apt_all_day">Hele dag</label>
                    </div>
                    <div class="row" id="apt_times">
                        <div class="col-6 mb-3">
                            <label class="form-label" for="apt_start">Van</label>
                            <input type="time" class="form-control" name="start_time" id="apt_start" value="09:00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label" for="apt_end">Tot</label>
                            <input type="time" class="form-control" name="end_time" id="apt_end" value="10:00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="apt_location">Locatie</label>
                        <input type="text" class="form-control" name="location" id="apt_location" maxlength="180" placeholder="Adres of videocall">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="apt_description">Toelichting</label>
                        <textarea class="form-control" name="description" id="apt_description" rows="3" placeholder="Wat je meeneemt, contactpersoon…"></textarea>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="open_google" id="apt_open_google" value="1">
                        <label class="form-check-label" for="apt_open_google">Na opslaan openen in Google Agenda</label>
                    </div>
                </div>
                <div class="modal-footer flex-wrap">
                    <button type="button" class="btn btn-outline-success" id="aptGoogleBtn">
                        <i class="bi bi-google"></i> Naar Google Agenda
                    </button>
                    <a class="btn btn-outline-secondary d-none" id="aptIcsBtn" href="#">
                        <i class="bi bi-download"></i> .ics
                    </a>
                    <button type="button" class="btn btn-outline-danger d-none" id="aptDeleteBtn">Verwijderen</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Opslaan</button>
                </div>
            </form>
            <form action="" method="post" id="aptDeleteForm" class="d-none">
                <?= csrf_field() ?>
            </form>
        </div>
    </div>
</div>
