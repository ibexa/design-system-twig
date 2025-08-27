import Base from '../shared/Base';

export default class Label extends Base {
    private _hasError = false;

    setHasError(value: boolean) {
        this._hasError = value;
        this._container.classList.toggle('ids-label--error', value);
    }

    getHasError(): boolean {
        return this._hasError;
    }
}
