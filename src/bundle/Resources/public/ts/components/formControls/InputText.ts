import Base from '../../shared/Base';
import HelperText from '../HelperText';
import InputText from '../inputs/InputText';
import Label from '../Label';

import IsEmptyStringValidator from '@ids-core/validators/IsEmptyStringValidator';
import type { TranslatorType } from '@ids-core/types/translator';
import type { ValidationResult } from '@ids-core/types/validation';
import ValidatorManager from '../../validators/ValidatorManager';

export default class FormControlInputText extends Base {
    private _labelInstance: Label | null = null;
    private _inputTextInstance: InputText;
    private _helperTextInstance: HelperText | null = null;
    private _validatorManager: ValidatorManager<string | number>;
    private _error = false;
    private _errorMessage = '';
    private _translator: TranslatorType = {
        trans: (key: string): string => key,
    }

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

// console.log(new IsEmptyStringValidator(this._translator));

//         if (this._inputTextInstance.getRequired()) {
//             this._validatorManager.addValidator(new IsEmptyStringValidator(this._translator));
//         }
    }

    setError(validationResult: ValidationResult) {
        const { isValid, messages } = validationResult;
        const isError = !isValid;

        if (this._error !== isError) {
            this._error = isError;
        }


        if (this._labelInstance) {
            this._labelInstance.error = isError;
        }

        if (this._helperTextInstance) {
            this._helperTextInstance.error = isError;
        }
    //     if (this._error === value) {
    //         return;
    //     }

    //     this._error = value;
    //     this._inputTextInstance.error = value;

    //     if (this._labelInstance) {
    //         this._labelInstance.error = value;
    //     }

    //     if (this._helperTextInstance) {
    //         this._helperTextInstance.error = value;
    //     }
    }

    // get error(): boolean {
    //     return this._error;
    // }

    // set errorMessage(value: string) {
    //     if (this._errorMessage === value) {
    //         return;
    //     }

    //     this._errorMessage = value;

    //     if (this._error && this._helperTextInstance) {
    //         this._helperTextInstance.message = value;
    //     }

    //     if (!this._error && this._helperTextInstance) {
    //         this._helperTextInstance.changeToDefaultMessage();
    //     }
    // }

    initChildren() {
        this._labelInstance?.init();
        this._inputTextInstance.init();
        this._helperTextInstance?.init();
    }

    initInputListeners() {
        this._inputTextInstance.getInputElement().addEventListener('input', ({ currentTarget }) => {
            if (!(currentTarget instanceof HTMLInputElement)) {
                throw new Error('FormControlInputText: Current target is not an HTMLInputElement.');
            }

            const validatorData = this._validatorManager.validate(currentTarget.value);

            this.error = !validatorData.isValid;
            this.errorMessage = validatorData.messages.join(', ');
        // });
    }

    init() {
        super.init();

        this.initChildren();
        this.initInputListeners();
    }
}