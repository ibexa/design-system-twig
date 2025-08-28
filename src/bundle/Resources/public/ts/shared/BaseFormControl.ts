import Base from './Base';
import HelperText from '../components/HelperText';
import Label from '../components/Label';

import ValidatorManager from '../validators/ValidatorManager';

export default abstract class BaseFormControl<T> extends Base {
    protected _labelInstance: Label | null = null;
    protected _helperTextInstance: HelperText | null = null;
    protected _validatorManager: ValidatorManager<T>;
    protected _hasError = false;
    protected _errorMessage = '';

    constructor(container: HTMLDivElement) {
        super(container);

        const labelContainer = this._container.querySelector<HTMLDivElement>('.ids-label');

        if (labelContainer) {
            this._labelInstance = new Label(labelContainer);
        }

        const helperTextContainer = this._container.querySelector<HTMLDivElement>('.ids-helper-text');

        if (helperTextContainer) {
            this._helperTextInstance = new HelperText(helperTextContainer);
        }

        this._validatorManager = new ValidatorManager();
    }

    setHasError(value: boolean) {
        if (this._hasError === value) {
            return;
        }

        this._hasError = value;

        if (this._labelInstance) {
            this._labelInstance.setHasError(value);
        }

        if (this._helperTextInstance) {
            this._helperTextInstance.setHasError(value);
        }
    }

    getHasError(): boolean {
        return this._hasError;
    }

    setErrorMessage(value: string) {
        if (this._errorMessage === value) {
            return;
        }

        this._errorMessage = value;

        if (this._helperTextInstance) {
            if (this._hasError) {
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
