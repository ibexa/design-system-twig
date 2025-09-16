import { Base } from '../partials';

type IconsTypes = 'default' | 'error';

export class HelperText extends Base {
    private _iconWrapper: HTMLDivElement;
    private _contentWrapper: HTMLDivElement;
    private _iconsTemplates: Record<IconsTypes, Node | null> = {
        default: null,
        error: null,
    };

    private _hasError = false;
    private _message = '';
    private _defaultMessage: string;

    constructor(container: HTMLElement) {
        super(container);

        const iconWrapper = this._container.querySelector<HTMLDivElement>('.ids-helper-text__icon-wrapper');
        const contentWrapper = this._container.querySelector<HTMLDivElement>('.ids-helper-text__content-wrapper');

        if (!iconWrapper || !contentWrapper) {
            throw new Error('HelperText: Required elements are missing in the container.');
        }

        this._iconWrapper = iconWrapper;
        this._contentWrapper = contentWrapper;
        this._defaultMessage = contentWrapper.textContent ?? '';

        const defaultIconTemplate = iconWrapper.querySelector<HTMLTemplateElement>(
            '.ids-helper-text__icon-template[data-ids-type="default"]',
        );
        const errorIconTemplate = iconWrapper.querySelector<HTMLTemplateElement>('.ids-helper-text__icon-template[data-ids-type="error"]');

        this._iconsTemplates = {
            default: defaultIconTemplate?.content.cloneNode(true) ?? null,
            error: errorIconTemplate?.content.cloneNode(true) ?? null,
        };
    }

    setDefaultMessage(value: string): void {
        this._defaultMessage = value;
    }

    setHasError(value: boolean): void {
        if (this._hasError === value) {
            return;
        }

        this._hasError = value;

        this._container.classList.toggle('ids-helper-text--error', value);

        const iconElement = this._iconWrapper.querySelector('.ids-helper-text__icon');

        if (!iconElement) {
            return;
        }

        const replacementIcon = value ? this._iconsTemplates.error : this._iconsTemplates.default;

        if (!replacementIcon) {
            throw new Error(`HelperText: Icon template for type "${value ? 'error' : 'default'}" is missing.`);
        }

        iconElement.replaceWith(replacementIcon.cloneNode(true));
    }

    setMessage(value: string): void {
        if (this._message === value) {
            return;
        }

        this._message = value;

        this._contentWrapper.textContent = value;
    }

    changeToDefaultMessage(): void {
        this.setMessage(this._defaultMessage);
    }
}
