import { BaseChoiceInput } from '../../partials';

export class ThreeStateCheckboxInput extends BaseChoiceInput {
    constructor(container: HTMLDivElement) {
        super(container);

        this.setIndeterminate(this._inputElement.classList.contains('ids-input--indeterminate'));
    }

    setIndeterminate(value: boolean) {
        this._inputElement.indeterminate = value;
        this._inputElement.classList.toggle('ids-input--indeterminate', value);

        if (value) {
            this._inputElement.checked = false;

            this._inputElement.dispatchEvent(new Event('input', { bubbles: true }));
            this._inputElement.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}
