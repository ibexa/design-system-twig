import { Base } from '../partials';

export class Label extends Base {
    private _hasError = false;

    setHasError(value: boolean): void {
        this._hasError = value;
        this._container.classList.toggle('ids-label--error', value);
    }

    getHasError(): boolean {
        return this._hasError;
    }
}
