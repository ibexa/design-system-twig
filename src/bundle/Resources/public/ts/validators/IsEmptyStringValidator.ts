import BaseValidator from './BaseValidator';

export default class IsEmptyStringValidator extends BaseValidator {
    getErrorMessage(): string {
        return /*@Desc("This field cannot be empty.")*/ 'ibexa.validators.is_empty_string';
    }

    validate(value: string): boolean {
        return value.trim() !== '';
    }
}
