import Base from './Base';

export default abstract class BaseCheckbox extends Base {
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
