import Base from '../../shared/Base';

export default class Checkbox extends Base {
    protected _inputElement: HTMLInputElement;

    constructor(container: HTMLDivElement) {
        super(container);

        const inputElement = container.querySelector<HTMLInputElement>('.ids-input');

        if (!inputElement) {
            throw new Error('Checkbox: Required elements are missing in the container.');
        }

        this._inputElement = inputElement;
    }
}
