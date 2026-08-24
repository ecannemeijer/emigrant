<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="page-hero mb-4">
    <div>
        <h1>Emigratie-checklist</h1>
        <p class="text-muted mb-0">Praktische stappen NL → IT. Dit is geen juridisch of fiscaal advies; controleer altijd de actuele regels.</p>
    </div>
    <div class="hero-progress">
        <span><?= (int) $done ?> / <?= (int) $total ?></span>
        <div class="progress">
            <div class="progress-bar" style="width: <?= $total ? round($done / $total * 100) : 0 ?>%"></div>
        </div>
    </div>
</div>

<?php foreach ($grouped as $category => $items): ?>
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0"><?= esc($category) ?></h5></div>
        <div class="list-group list-group-flush">
            <?php foreach ($items as $item): ?>
                <?php
                    $guide = \App\Libraries\ChecklistGuide::guide($item['item_key']);
                    $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($item['notes'] ?? ''))));
                    $hasNote = $plain !== '';
                    $preview = $hasNote ? (mb_strlen($plain) > 80 ? mb_substr($plain, 0, 80) . '…' : $plain) : '';
                    $guideId = 'guide-' . (int) $item['id'];
                ?>
                <div class="list-group-item checklist-item">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <form action="/checklist/toggle/<?= $item['id'] ?>" method="post" class="d-flex gap-2 flex-grow-1">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm <?= $item['done'] ? 'btn-success' : 'btn-outline-secondary' ?>" type="submit" title="Afvinken">
                                <i class="bi <?= $item['done'] ? 'bi-check-circle-fill' : 'bi-circle' ?>"></i>
                            </button>
                            <div>
                                <strong class="<?= $item['done'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                    <?= esc($item['title']) ?>
                                </strong>
                                <?php if (!empty($guide['summary'])): ?>
                                    <div class="small text-muted mt-1"><?= esc($guide['summary']) ?></div>
                                <?php endif; ?>
                                <?php if ($hasNote): ?>
                                    <div class="small mt-1 text-success"><i class="bi bi-sticky"></i> <?= esc($preview) ?></div>
                                <?php endif; ?>
                            </div>
                        </form>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-success" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#<?= $guideId ?>"
                                    aria-expanded="false">
                                <i class="bi bi-info-circle"></i> Uitleg
                            </button>
                            <button class="btn btn-sm <?= $hasNote ? 'btn-primary' : 'btn-outline-primary' ?> js-note-btn"
                                    type="button"
                                    data-id="<?= (int) $item['id'] ?>"
                                    data-title="<?= esc($item['title'], 'attr') ?>">
                                <i class="bi bi-pencil-square"></i> Notitie
                            </button>
                        </div>
                    </div>
                    <div class="collapse mt-3" id="<?= $guideId ?>">
                        <div class="checklist-guide">
                            <?php if (!empty($guide['how'])): ?>
                                <h6>Hoe regel je dit?</h6>
                                <ul><?php foreach ($guide['how'] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ul>
                            <?php endif; ?>
                            <?php if (!empty($guide['where'])): ?>
                                <h6>Waar?</h6>
                                <ul><?php foreach ($guide['where'] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ul>
                            <?php endif; ?>
                            <?php if (!empty($guide['watch'])): ?>
                                <h6>Waar let je op?</h6>
                                <ul><?php foreach ($guide['watch'] as $line): ?><li><?= esc($line) ?></li><?php endforeach; ?></ul>
                            <?php endif; ?>
                            <?php if (!empty($guide['links'])): ?>
                                <h6>Handige links</h6>
                                <ul class="mb-0">
                                    <?php foreach ($guide['links'] as $link): ?>
                                        <li><a href="<?= esc($link['url'], 'attr') ?>" target="_blank" rel="noopener"><?= esc($link['label']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="noteForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="noteModalLabel">Notitie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted" id="noteModalItem"></p>
                    <div class="editor-toolbar" role="toolbar" aria-label="Opmaak">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="bold" title="Vet"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="italic" title="Cursief"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="underline" title="Onderstrepen"><i class="bi bi-type-underline"></i></button>
                        <span class="toolbar-sep"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertUnorderedList" title="Opsomming"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertOrderedList" title="Nummering"><i class="bi bi-list-ol"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="formatBlock" data-value="h3" title="Kop"><i class="bi bi-type-h3"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="formatBlock" data-value="blockquote" title="Citaat"><i class="bi bi-quote"></i></button>
                        <span class="toolbar-sep"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="noteLinkBtn" title="Link"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="removeFormat" title="Opmaak wissen"><i class="bi bi-eraser"></i></button>
                    </div>
                    <div id="noteEditor" class="note-editor" contenteditable="true" role="textbox" aria-multiline="true" data-placeholder="Schrijf hier je notitie…"></div>
                    <textarea name="notes" id="noteHtml" class="d-none"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const checklistNotes = <?= json_encode($notesById ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('noteModal');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('noteForm');
    const editor = document.getElementById('noteEditor');
    const hidden = document.getElementById('noteHtml');
    const titleEl = document.getElementById('noteModalLabel');
    const itemEl = document.getElementById('noteModalItem');

    document.querySelectorAll('.js-note-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            form.action = '/checklist/note/' + id;
            titleEl.textContent = 'Notitie';
            itemEl.textContent = this.dataset.title || '';
            editor.innerHTML = checklistNotes[id] || '';
            modal.show();
            setTimeout(function () { editor.focus(); }, 250);
        });
    });

    document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            editor.focus();
            const cmd = this.dataset.cmd;
            const value = this.dataset.value || null;
            document.execCommand(cmd, false, value);
        });
    });

    document.getElementById('noteLinkBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const url = window.prompt('Link (https://…)', 'https://');
        if (!url) return;
        editor.focus();
        document.execCommand('createLink', false, url);
    });

    form.addEventListener('submit', function () {
        hidden.value = editor.innerHTML;
    });
});
</script>
<?= $this->endSection() ?>
