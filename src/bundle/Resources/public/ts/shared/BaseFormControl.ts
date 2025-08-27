import Base from './Base';
import HelperText from '../components/HelperText';
import Label from '../components/Label';

import ValidatorManager from '../validators/ValidatorManager';

export const BASE_EVENTS = {
    INITIALIZED: 'ids:component:initialized',
} as const;

export default abstract class BaseFormControl<T> extends Base {
    protected _labelInstance: Label | null = null;
    protected _helperTextInstance: HelperText | null = null;
    protected _validatorManager: ValidatorManager<T>;
    protected _error = false;
    protected _errorMessage = '';

    constructor(container: HTMLDivElement) {
        super(container);

        const labelContainer = container.querySelector<HTMLDivElement>('.ids-label');

        if (labelContainer) {
            this._labelInstance = new Label(labelContainer);
        }

        const helperTextContainer = container.querySelector<HTMLDivElement>('.ids-helper-text');

        if (helperTextContainer) {
            this._helperTextInstance = new HelperText(helperTextContainer);
        }

        this._validatorManager = new ValidatorManager();
    }

    setError(value: boolean) {
        if (this._error === value) {
            return;
        }

        this._error = value;

        if (this._labelInstance) {
            this._labelInstance.setError(value);
        }

        if (this._helperTextInstance) {
            this._helperTextInstance.setError(value);
        }
    }

    getError(): boolean {
        return this._error;
    }

    setErrorMessage(value: string) {
        if (this._errorMessage === value) {
            return;
        }

        this._errorMessage = value;

        if (this._helperTextInstance) {
            if (this._error) {
                this._helperTextInstance.setMessage(value);
            } else {
                this._helperTextInstance.changeToDefaultMessage();
            }
        }
    }

    initChildren() {
        this._labelInstance?.init();
        this._helperTextInstance?.init();
    }

    init() {
        super.init();

        this.initChildren();
    }
}
