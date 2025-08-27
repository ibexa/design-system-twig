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
    private _hasError = false;
    private _errorMessage = '';
    private _translator: TranslatorType = {
        trans: (key: string): string => key,
    };

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

        if (this._inputTextInstance.getIsRequired()) {
            this._validatorManager.addValidator(new IsEmptyStringValidator(this._translator));
        }
    }

    setError(validationResult: ValidationResult): void {
        const { isValid, messages } = validationResult;
        const errorMessage = messages.join(', ');
        const isError = !isValid;

        if (this._hasError !== isError) {
            this._hasError = isError;

            this._inputTextInstance.setError(isError);
        }

        if (this._labelInstance) {
            this._labelInstance.setHasError(isError);
        }

        if (this._helperTextInstance) {
            this._helperTextInstance.setHasError(isError);
        }

        if (this._errorMessage !== errorMessage) {
            this._errorMessage = errorMessage;

            if (this._helperTextInstance) {
                if (isError) {
                    this._helperTextInstance.setMessage(errorMessage);
                } else {
                    this._helperTextInstance.changeToDefaultMessage();
                }
            }
        }
    }

    initChildren(): void {
        this._labelInstance?.init();
        this._inputTextInstance.init();
        this._helperTextInstance?.init();
    }

    initInputListeners(): void {
        this._inputTextInstance.getInputElement().addEventListener('input', ({ currentTarget }) => {
            if (!(currentTarget instanceof HTMLInputElement)) {
                throw new Error('FormControlInputText: Current target is not an HTMLInputElement.');
            }

            const validationResult = this._validatorManager.validate(currentTarget.value);

            this.setError(validationResult);
        });
    }

    init(): void {
        super.init();

        this.initChildren();
        this.initInputListeners();
    }
}
