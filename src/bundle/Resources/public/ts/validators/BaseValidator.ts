// eslint-disable-next-line @typescript-eslint/no-unnecessary-type-parameters
export default abstract class BaseValidator<T> {
    abstract getErrorMessage(): string;

    abstract validate(_value: T): boolean;
}
