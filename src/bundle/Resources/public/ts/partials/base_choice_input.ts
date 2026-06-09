import { Base } from './base';

export abstract class BaseChoiceInput extends Base {
    protected _inputElement: HTMLInputElement;

    constructor(inputElement: HTMLInputElement) {
        super(inputElement);

        this._inputElement = inputElement;
    }
}
