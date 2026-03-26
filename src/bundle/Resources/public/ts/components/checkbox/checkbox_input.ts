import { BaseChoiceInput } from '../../partials';

export class CheckboxInput extends BaseChoiceInput {
    constructor(inputElement: HTMLInputElement) {
        super(inputElement);

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
