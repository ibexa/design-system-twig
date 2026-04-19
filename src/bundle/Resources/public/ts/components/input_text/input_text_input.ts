import { Base } from '../../partials';

export class InputTextInput extends Base {
    private _inputElement: HTMLInputElement;
    private _actionsElement: HTMLDivElement;
    private _clearBtnElement: HTMLButtonElement;
    private _passwordTogglerElement: HTMLButtonElement | null;
    private _passwordShowIconElement: HTMLElement | null;
    private _passwordHideIconElement: HTMLElement | null;

    constructor(container: HTMLDivElement) {
        super(container);

        const actionsElement = this._container.querySelector<HTMLDivElement>('.ids-input-text__actions');
        const inputElement = this._container.querySelector<HTMLInputElement>('.ids-input-text__source .ids-input');
        const clearBtnElement = actionsElement?.querySelector<HTMLButtonElement>('.ids-clear-btn');
        const passwordTogglerElement = actionsElement?.querySelector<HTMLButtonElement>('.ids-input-text__password-toggler') ?? null;

        if (!actionsElement || !inputElement || !clearBtnElement) {
            throw new Error('InputTextInput: Required elements are missing in the container.');
        }

        this._actionsElement = actionsElement;
        this._inputElement = inputElement;
        this._clearBtnElement = clearBtnElement;
        this._passwordTogglerElement = passwordTogglerElement;
        this._passwordShowIconElement = passwordTogglerElement?.querySelector<HTMLElement>('.ids-input-text__password-icon--show') ?? null;
        this._passwordHideIconElement = passwordTogglerElement?.querySelector<HTMLElement>('.ids-input-text__password-icon--hide') ?? null;
    }

    setError(value: boolean): void {
        this._inputElement.classList.toggle('ids-input--error', value);
    }

    getInputElement(): HTMLInputElement {
        return this._inputElement;
    }

    getIsRequired(): boolean {
        return this._inputElement.required;
    }

    private _updateInputPadding() {
        const actionsWidth = this._actionsElement.offsetWidth;

        this._inputElement.style.paddingRight = `${actionsWidth}px`;
    }

    changeValue(value: string) {
        const isNewValue = this._inputElement.value !== value;

        if (isNewValue) {
            this._inputElement.value = value;

            this._inputElement.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    updateClearBtnVisibility() {
        const isEmpty = this._inputElement.value === '';

        this._clearBtnElement.parentElement?.classList.toggle('ids-input-text__action--hidden', isEmpty);
    }

    initInputListeners() {
        this._inputElement.addEventListener('input', () => {
            this.updateClearBtnVisibility();
            this._updateInputPadding();
        });
    }

    initClearBtn() {
        this._clearBtnElement.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            event.stopPropagation();

            this.changeValue('');
        });
    }

    initPasswordToggler() {
        if (!this._passwordTogglerElement) {
            return;
        }

        this._passwordTogglerElement.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            event.stopPropagation();

            const isPasswordType = this._inputElement.type === 'password';

            this._inputElement.type = isPasswordType ? 'text' : 'password';
            this._passwordShowIconElement?.classList.toggle('d-none', isPasswordType);
            this._passwordHideIconElement?.classList.toggle('d-none', !isPasswordType);
        });
    }

    init() {
        super.init();

        this.initInputListeners();
        this.initClearBtn();
        this.initPasswordToggler();
        this._updateInputPadding();
    }
}
