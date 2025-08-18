export default abstract class BaseValidator {
    abstract getErrorMessage(): string;

    abstract validate(_value: unknown): boolean;
}

export type BaseValidatorType = typeof BaseValidator;
