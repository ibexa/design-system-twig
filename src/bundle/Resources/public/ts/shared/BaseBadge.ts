import Base from './Base';

const MAX_BADGE_VALUE = 99;

export default abstract class BaseBadge extends Base {
    private value = '';

    private _parseValue(text: string | null): number | null {
        if (text === null || text.trim() === '') {
            return null;
        }
        const numericValue = parseInt(text, 10);
        return isNaN(numericValue) ? null : numericValue;
    }

    private _formatValue(value: number): string {
        return value > MAX_BADGE_VALUE ? '99+' : value.toString();
    }

    setValue(value: number | null): void {
        if (value === null) {
            this.value = '';
            return;
        }
        this.value = this._formatValue(value);
    }

    getValue(): number | null {
        return this._parseValue(this.value);
    }
}
