<?php $returnTo = $returnTo ?? 'list'; ?>
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content reno-cat-modal">
            <form action="/renovation/category" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="category_id" value="">
                <input type="hidden" name="return_to" value="<?= esc($returnTo, 'attr') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Nieuwe categorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Een categorie is een groep, zoals Keuken of Dak. Posten (kosten) voeg je daarna toe.</p>
                    <label class="form-label" for="category_name">Naam</label>
                    <input type="text" class="form-control" name="name" id="category_name" required maxlength="80" placeholder="Bijv. Keuken">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-folder-plus"></i> Categorie opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
