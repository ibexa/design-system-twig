import BaseCheckbox from '../../shared/BaseCheckbox';

export default class ThreeStateCheckbox extends BaseCheckbox {
    private _indeterminate = false;

    constructor(container: HTMLDivElement) {
        super(container);

        this.indeterminate = this._inputElement.classList.contains('ids-input--indeterminate');
    }

    set indeterminate(value: boolean) {
        this._indeterminate = value;
        this._inputElement.indeterminate = value;
        this._inputElement.classList.toggle('ids-input--indeterminate', value);

        if (value) {
            this._inputElement.checked = false;

            this._inputElement.dispatchEvent(new Event('input', { bubbles: true }));
            this._inputElement.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}
