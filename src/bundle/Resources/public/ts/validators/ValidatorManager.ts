import BaseValidator from './BaseValidator';

export default class ValidatorManager {
    private _validators: BaseValidator[];

    constructor(validators: BaseValidator[] = []) {
        this._validators = validators;
    }

    addValidator(validator: BaseValidator): void {
        this._validators.push(validator);
    }

    removeValidator(validator: BaseValidator): void {
        this._validators = this._validators.filter((savedValidator) => savedValidator !== validator);
    }

    validate(value: unknown) {
        const errors = this._validators
            .filter((validator: BaseValidator) => !validator.validate(value))
            .map((validator: BaseValidator) => validator.getErrorMessage());

        return { isValid: !errors.length, messages: errors };
    }
}

export type ValidatorManagerType = InstanceType<typeof ValidatorManager>;
