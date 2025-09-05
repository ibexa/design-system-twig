import Base from '../shared/Base';

export default class BaseBadge extends Base {
    private _value = '';
    private _maxBadgeValue: number;

    constructor(container: HTMLDivElement) {
        super(container);

        const _maxBadgeValue = this._container.dataset.ids_MaxBadgeValue;

        if (!_maxBadgeValue || isNaN(parseInt(_maxBadgeValue, 10))) {
            throw new Error('There is no proper max badge value defined for this badge!');
        }
        const parsedMax = parseInt(_maxBadgeValue, 10);

        this._maxBadgeValue = parsedMax;

        const initialValue = this._parseValue(this._container.textContent);

        if (initialValue === null) {
            throw new Error('No value found for this badge!');
        }

        this.setValue(initialValue);
    }

    private _parseValue(text: string | null): number | null {
        if (text === null || text.trim() === '') {
            return null;
        }

        const numericValue = parseInt(text, 10);

        return isNaN(numericValue) ? null : numericValue;
    }

    private _formatValue(value: number): string {
        return value > this._maxBadgeValue ? `${this._maxBadgeValue}+` : value.toString();
    }

    setValue(value: number | null): void {
        if (value === null) {
            this._value = '';
            this._container.textContent = '';

            return;
        }

        this._value = this._formatValue(value);
        this._container.textContent = this._value;
        this._container.classList.toggle('ids-badge--wide', value > this._maxBadgeValue);
    }

    getValue(): number | null {
        return this._parseValue(this._value);
    }

    setMaxValue(max: number): void {
        this._maxBadgeValue = max;

        const currentValue = this.getValue();

        if (currentValue !== null && currentValue > this._maxBadgeValue) {
            this.setValue(currentValue);
        }
    }

    getMaxValue(): number {
        return this._maxBadgeValue;
    }
}
