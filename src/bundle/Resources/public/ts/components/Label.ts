import Base from '../shared/Base';

export default class Label extends Base {
    private _error = false;

    setError(value: boolean) {
        this._error = value;
        this._container.classList.toggle('ids-label--error', value);
    }

    getError(): boolean {
        return this._error;
    }
}
