class ConfirmationModal {
    constructor() {
        this.modal = document.getElementById('almGenericConfirmModal');
        this.title = document.getElementById('confirmModalTitle');
        this.message = document.getElementById('confirmModalMessage');
        this.confirmBtn = document.getElementById('confirmActionBtn');
        this.cancelBtn = document.getElementById('cancelActionBtn');
        this.closeBtn = this.modal.querySelector('.alm-modal-close');
        this.confirmBtnIcon = document.getElementById('confirmBtnIcon');
        this.confirmBtnText = document.getElementById('confirmBtnText');

        this.currentAction = null;
        this.currentParams = null;

        this._setupListeners();
    }

    _setupListeners() {
        [this.closeBtn, this.cancelBtn].forEach(btn => {
            btn.addEventListener('click', () => this.hide());
        });

        this.confirmBtn.addEventListener('click', () => {
            if (typeof this.currentAction === 'function') {
                this.currentAction(this.currentParams);
            }
            this.hide();
        });

        window.addEventListener('click', (e) => {
            if (e.target === this.modal) this.hide();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.hide();
        });
    }

    show({ title, icon, message, confirmText, confirmIcon, confirmColor, action, params }) {
        this.title.innerHTML = `<i class="${icon}"></i> ${title}`;
        this.message.textContent = message;
        this.confirmBtnText.textContent = confirmText;
        this.confirmBtnIcon.className = confirmIcon;
        this.confirmBtn.style.backgroundColor = confirmColor;

        this.currentAction = action;
        this.currentParams = params;

        this.modal.classList.add('show');
    }

    hide() {
        this.modal.classList.remove('show');
        this.currentAction = null;
        this.currentParams = null;
    }
}

const confirmationModal = new ConfirmationModal();
