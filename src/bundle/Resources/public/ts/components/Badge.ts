import Base from '../shared/Base';

const THRESHOLD = {
    medium: 100,
    small: 10,
};

enum BadgeSize {
    Medium = 'medium',
    Small = 'small',
}

export default class BaseBadge extends Base {
    private _value = '';
    private _maxBadgeValue: number;

    constructor(container: HTMLDivElement) {
        super(container);

        const _maxBadgeValue = this._container.dataset.ids_MaxBadgeValue ?? '';
        const parsedMax = parseInt(_maxBadgeValue, 10);

        if (!_maxBadgeValue || isNaN(parsedMax)) {
            throw new Error('There is no proper max badge value defined for this badge!');
        }

        this._maxBadgeValue = parsedMax;

        const initialValue = this._parseValue(this._container.textContent);

        if (initialValue === null) {
            throw new Error('No value found for this badge!');
        }

        this.setValue(initialValue);
    }

    private _parseValue(badgeContent: string | null): number | null {
        if (badgeContent === null || badgeContent.trim() === '') {
            return null;
        }

        const numericValue = parseInt(badgeContent, 10);

        return isNaN(numericValue) ? null : numericValue;
    }

    private _formatValue(value: number): string {
        return value > this._maxBadgeValue ? `${this._maxBadgeValue.toString()}+` : value.toString();
    }

    setValue(value: number | null): void {
        if (value === null) {
            this._value = '';
            this._container.textContent = '';

            return;
        }

        this._value = this._formatValue(value);
        this._container.textContent = this._value;

        const size = this._container.classList.contains('.ids-badge--small') ? BadgeSize.Small : BadgeSize.Medium;

        this._container.classList.toggle('ids-badge--wide', value >= THRESHOLD[size]);
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
