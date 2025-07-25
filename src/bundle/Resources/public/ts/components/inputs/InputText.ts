import Base from '../../shared/Base';

export default class InpuText extends Base {
    private _inputElement: HTMLInputElement;
    private _actionsElement: HTMLDivElement;
    private _clearBtnElement: HTMLButtonElement;

    constructor(container: HTMLDivElement) {
        super(container);

        this._actionsElement = container.querySelector('.ids-input-text__actions') as HTMLDivElement;
        this._inputElement = container.querySelector('.ids-input-text__source .ids-input') as HTMLInputElement;
        this._clearBtnElement = this._actionsElement.querySelector('.ids-clear-btn') as HTMLButtonElement;
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
