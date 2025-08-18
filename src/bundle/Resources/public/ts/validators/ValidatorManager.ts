import BaseValidator from './BaseValidator';

export interface ValidateReturnType {
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

    validate(value: T): ValidateReturnType {
        const errors = this._validators
            .filter((validator: BaseValidator<T>) => !validator.validate(value))
            .map((validator: BaseValidator<T>) => validator.getErrorMessage());

        return { isValid: !errors.length, messages: errors };
    }
}
