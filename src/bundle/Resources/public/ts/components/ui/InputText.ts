import Base from '../../shared/Base';

export default class InpuText extends Base {
    private _inputElement: HTMLInputElement | null;
    private _actionsElement: HTMLElement | null;
    private _clearBtnElement: HTMLElement | null;

    constructor(container: HTMLElement) {
        super(container);

        this._actionsElement = container.querySelector<HTMLElement>('.ids-input-text__actions');
        this._inputElement = container.querySelector<HTMLInputElement>('.ids-input-text__source .ids-input');

        if (!this._actionsElement) {
            throw new Error('Actions element not found in the container!');
        }

        this._clearBtnElement = this._actionsElement.querySelector<HTMLElement>('.ids-clear-btn');
    }

    private _updateInputPadding() {
        if (!this._inputElement) {
            throw new Error('Input element not found in the container!');
        }

        if (!this._actionsElement) {
            throw new Error('Actions element not found in the container!');
        }

        const actionsWidth = this._actionsElement.offsetWidth;

        this._inputElement.style.paddingRight = `${actionsWidth.toString()}px`;
    }

    changeValue(value: string) {
        if (!this._inputElement) {
            throw new Error('Input element not found in the container!');
        }

        const isNewValue = this._inputElement.value !== value;

        if (isNewValue) {
            this._inputElement.value = value;

            this._inputElement.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    updateClearBtnVisibility() {
        if (!this._clearBtnElement) {
            throw new Error('Clear button element not found in the container!');
        }

        const isEmpty = this._inputElement?.value === '';

        this._clearBtnElement.parentElement?.classList.toggle('ids-input-text__action--hidden', isEmpty);
    }

    initInputListeners() {
        if (!this._inputElement) {
            throw new Error('Input element not found in the container!');
        }

        this._inputElement.addEventListener('input', () => {
            this.updateClearBtnVisibility();
            this._updateInputPadding();
        });
    }

    initClearBtn() {
        if (!this._clearBtnElement) {
            throw new Error('Clear button element not found in the container!');
        }

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
