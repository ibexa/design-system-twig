import { Base } from '../../partials';

export class InputTextInput extends Base {
    private _inputElement: HTMLInputElement;
    private _actionsElement: HTMLDivElement;
    private _clearBtnElement: HTMLButtonElement;

    constructor(container: HTMLDivElement) {
        super(container);

        const actionsElement = this._container.querySelector<HTMLDivElement>('.ids-input-text__actions');
        const inputElement = this._container.querySelector<HTMLInputElement>('.ids-input-text__source .ids-input');
        const clearBtnElement = actionsElement?.querySelector<HTMLButtonElement>('.ids-clear-btn');

        if (!actionsElement || !inputElement || !clearBtnElement) {
            throw new Error('InputTextInput: Required elements are missing in the container.');
        }

        this._actionsElement = actionsElement;
        this._inputElement = inputElement;
        this._clearBtnElement = clearBtnElement;
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

        this._inputElement.style.paddingRight = `${actionsWidth.toString()}px`;
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

    init() {
        super.init();

        this.initInputListeners();
        this.initClearBtn();
        this._updateInputPadding();
    }
}
