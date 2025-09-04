import Base from '../shared/Base';

export default class BaseBadge extends Base {
    private value = '';
    private maxBadgeValue = 0;

    constructor(container: HTMLElement) {
        super(container);

        const maxBadgeValue = this._container.dataset.idsMaxBadgeValue;
        if (maxBadgeValue) {
            const parsedMax = parseInt(maxBadgeValue, 10);
            if (!isNaN(parsedMax)) {
                this.maxBadgeValue = parsedMax;
            }
        }

        const initialValue = this._parseValue(this._container.textContent);
        if (initialValue !== null) {
            this.setValue(initialValue);
        }
    }

    private _parseValue(text: string | null): number | null {
        if (text === null || text.trim() === '') {
            return null;
        }

        const numericValue = parseInt(text, 10);

        return isNaN(numericValue) ? null : numericValue;
    }

    private _formatValue(value: number): string {
        return value > this.maxBadgeValue ? `${this.maxBadgeValue}+` : value.toString();
    }

    setValue(value: number | null): void {
        if (value === null) {
            this.value = '';
            this._container.textContent = '';

            return;
        }

        this.value = this._formatValue(value);
        this._container.textContent = this.value;
        this._container.classList.toggle('ids-badge--wide', value > this.maxBadgeValue);
    }

    getValue(): number | null {
        return this._parseValue(this.value);
    }

    setMaxValue(max: number): void {
        this.maxBadgeValue = max;

        const currentValue = this.getValue();

        if (currentValue !== null && currentValue > this.maxBadgeValue) {
            this.setValue(currentValue);
        }
    }

    getMaxValue(): number {
        return this.maxBadgeValue;
    }
}
