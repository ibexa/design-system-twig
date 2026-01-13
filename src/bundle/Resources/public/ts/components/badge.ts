import { Base } from '../partials';

enum BadgeSize {
    Medium = 'medium',
    Small = 'small',
}
enum BadgeVariant {
    String = 'string',
    Number = 'number',
}

const THRESHOLD = {
    [BadgeSize.Medium]: 100,
    [BadgeSize.Small]: 10,
};

const STRING_THRESHOLD = {
    [BadgeSize.Medium]: 3,
    [BadgeSize.Small]: 2,
};

export default class Badge extends Base {
    private value = '0';
    private maxBadgeValue: number;
    private variant: BadgeVariant;

    constructor(container: HTMLDivElement) {
        super(container);

        const maxBadgeValue = this._container.dataset.idsMaxBadgeValue ?? '';
        const parsedMax = parseInt(maxBadgeValue, 10);

        if (isNaN(parsedMax)) {
            throw new Error('There is no proper max badge value defined for this badge!');
        }

        this.maxBadgeValue = parsedMax;

        const variantValue = this._container.dataset.idsVariant;

        this.variant = variantValue === BadgeVariant.Number || variantValue === BadgeVariant.String ? variantValue : BadgeVariant.String;

        const initialValue = this._container.textContent?.trim() ?? '';

        if (initialValue === '') {
            throw new Error('No value found for this badge!');
        }

        this.setValue(initialValue);
    }

    private parseValue(badgeContent: string | null): number | null {
        if (badgeContent === null || badgeContent.trim() === '') {
            return null;
        }

        const numericValue = parseInt(badgeContent, 10);

        return isNaN(numericValue) ? null : numericValue;
    }

    setValue(value: string): void {
        this.value = value;
    }

    renderContent(): void {
        const content = this.getFormattedValue();
        const size = this.getSize();

        this._container.textContent = content;

        const isStretched =
            this.variant === BadgeVariant.Number
                ? (this.parseValue(this.value) ?? 0) >= THRESHOLD[size]
                : this.value.length >= STRING_THRESHOLD[size];

        this._container.classList.toggle('ids-badge--stretched', isStretched);
    }

    getFormattedValue(): string {
        if (this.variant === BadgeVariant.String) {
            return this.value;
        }

        const numericValue = this.parseValue(this.value) ?? 0;

        return numericValue > this.maxBadgeValue ? `${this.maxBadgeValue}+` : numericValue.toString();
    }

    getValue(): string | null {
        return this.value;
    }

    getSize(): BadgeSize {
        return this._container.classList.contains('.ids-badge--small') ? BadgeSize.Small : BadgeSize.Medium;
    }

    setMaxValue(max: number): void {
        this.maxBadgeValue = max;

        const currentValue = this.parseValue(this.getValue()) ?? 0;

        if (currentValue > this.maxBadgeValue) {
            this.setValue(currentValue.toString());
        }
    }

    getMaxValue(): number {
        return this.maxBadgeValue;
    }
}
