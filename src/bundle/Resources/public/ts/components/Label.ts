import Base from '../shared/Base';

export default class Label extends Base {
    private _error = false;

    set error(value: boolean) {
        this._error = value;
        this.container.classList.toggle('ids-label--error', value);
    }

    get error(): boolean {
        return this._error;
    }
}
