export default abstract class BaseValidator<T> {
    abstract getErrorMessage(): string;

    abstract validate(value: T): boolean;
}
