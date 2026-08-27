<style>
    #branchDebtsModal .modal-dialog {
        max-width: 900px;
    }
    #branchDebtsModal .modal-content {
        max-height: calc(100vh - 120px);
    }
    #branchDebtsModal .modal-body {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
</style>

<div class="modal fade" id="branchDebtsModal" tabindex="-1" aria-labelledby="branchDebtsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="branchDebtsModalLabel">{{ __('messages.branch_debts') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="branch-debts-modal-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-3">{{ __('messages.loading') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-action="branch-debts"]');
        if (!button) {
            return;
        }

        event.preventDefault();
        const url = button.dataset.url;
        const modalElement = document.getElementById('branchDebtsModal');
        const modalBody = document.getElementById('branch-debts-modal-body');

        if (!url || !modalElement || !modalBody) {
            return;
        }

        const modal = new bootstrap.Modal(modalElement);
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-3">{{ __('messages.loading') }}</div>
            </div>
        `;
        modal.show();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            credentials: 'same-origin',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(function (html) {
                modalBody.innerHTML = html;
            })
            .catch(function () {
                modalBody.innerHTML = '<div class="alert alert-danger">{{ __('messages.branch_debts_load_error') }}</div>';
            });
    });
</script>
