import HelperText, { HelperTextType } from '../HelperText';
import InputText, { InputTextType } from '../inputs/InputText';
import Label, { LabelType } from '../Label';
import Base from '../../shared/Base';

import ValidatorManager, { ValidatorManagerType } from '../../validators/ValidatorManager';
import IsEmptyStringValidator from '../../validators/IsEmptyStringValidator';

export default class FormControlInputText extends Base {
    private _labelInstance: LabelType | null = null;
    private _inputTextInstance: InputTextType;
    private _helperTextInstance: HelperTextType | null = null;
    private _validatorManager: ValidatorManagerType;
    private _error = false;
    private _errorMessage = '';

    constructor(container: HTMLDivElement) {
        super(container);

        const inputTextContainer = container.querySelector<HTMLDivElement>('.ids-input-text');

        if (!inputTextContainer) {
            throw new Error('FormControlInputText: Required elements are missing in the container.');
        }

        const labelContainer = container.querySelector<HTMLDivElement>('.ids-label');

        if (labelContainer) {
            this._labelInstance = new Label(labelContainer);
        }

        const helperTextContainer = container.querySelector<HTMLDivElement>('.ids-helper-text');

        if (helperTextContainer) {
            this._helperTextInstance = new HelperText(helperTextContainer);
        }

        this._inputTextInstance = new InputText(inputTextContainer);
        this._validatorManager = new ValidatorManager();

        if (this._inputTextInstance.required) {
            const isEmptyStringValidator = new IsEmptyStringValidator();

            this._validatorManager.addValidator(isEmptyStringValidator);
        }
    }

    set error(value) {
        if (this._error === value) {
            return;
        }

        this._error = value;
        this._inputTextInstance.error = value;

        if (this._labelInstance) {
            this._labelInstance.error = value;
        }

        if (this._helperTextInstance) {
            this._helperTextInstance.error = value;
        }
    }

    get error(): boolean {
        return this._error;
    }

    set errorMessage(value: string) {
        if (this._errorMessage === value) {
            return;
        }

        this._errorMessage = value;

        if (this._error && this._helperTextInstance) {
            this._helperTextInstance.message = value;
        }

        if (!this._error && this._helperTextInstance) {
            this._helperTextInstance.changeToDefaultMessage();
        }
    }

    initChildren() {
        this._labelInstance?.init();
        this._inputTextInstance.init();
        this._helperTextInstance?.init();
    }

    initInputListeners() {
        this._inputTextInstance.inputElement.addEventListener('input', ({ currentTarget }) => {
            if (!(currentTarget instanceof HTMLInputElement)) {
                throw new Error('FormControlInputText: Current target is not an HTMLInputElement.');
            }

            const validatorData = this._validatorManager.validate(currentTarget.value);

            this.error = !validatorData.isValid;
            this.errorMessage = validatorData.messages.join(', ');
        });
    }

    init() {
        super.init();

        this.initChildren();
        this.initInputListeners();
    }
}
