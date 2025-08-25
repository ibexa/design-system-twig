import BaseValidator from './BaseValidator';

export interface ValidationResult {
    isValid: boolean;
    messages: string[];
}

export default class ValidatorManager<T> {
    private _validators: BaseValidator<T>[];

    constructor(validators: BaseValidator<T>[] = []) {
        this._validators = validators;
    }

    addValidator(validator: BaseValidator<T>): void {
        this._validators.push(validator);
    }

    removeValidator(validator: BaseValidator<T>): void {
        this._validators = this._validators.filter((savedValidator) => savedValidator !== validator);
    }

    validate(value: T): ValidationResult {
        const errors = this._validators.reduce((errorsAcc: string[], validator) => {
            if (!validator.validate(value)) {
                return [...errorsAcc, validator.getErrorMessage()];
            }

            return errorsAcc;
        }, []);

        return { isValid: !errors.length, messages: errors };
    }
}
