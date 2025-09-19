import { Base } from '../partials';

enum BadgeSize {
    Medium = 'medium',
    Small = 'small',
}

const THRESHOLD = {
    [BadgeSize.Medium]: 100,
    [BadgeSize.Small]: 10,
};

export default class Badge extends Base {
    private _value = 0;
    private _maxBadgeValue: number;

    constructor(container: HTMLDivElement) {
        super(container);

        const _maxBadgeValue = this._container.dataset.idsMaxBadgeValue ?? '';
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

    setValue(value: number): void {
        this._value = value;
    }

    renderContent(): void {
        const content = this.getValueRestrictedByMaxValue();
        const size = this.getSize();

        this._container.textContent = content;
        this._container.classList.toggle('ids-badge--stretched', this._value >= THRESHOLD[size]);
    }

    getValueRestrictedByMaxValue(): string {
        return this._value > this._maxBadgeValue ? `${this._maxBadgeValue.toString()}+` : this._value.toString();
    }

    getValue(): number | null {
        return this._value;
    }

    getSize(): BadgeSize {
        return this._container.classList.contains('.ids-badge--small') ? BadgeSize.Small : BadgeSize.Medium;
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
