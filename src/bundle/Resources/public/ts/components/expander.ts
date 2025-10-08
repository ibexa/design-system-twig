import { Base } from '../partials';

type ExpandHandlerType = (isExpanded: boolean) => void;

export class Expander extends Base {
    private _expandHandler: ExpandHandlerType | undefined;
    private _hasLabel: boolean;
    private _collapseLabel: string | undefined;
    private _expandLabel: string | undefined;
    private _labelContainer: HTMLElement | null;

    constructor(container: HTMLElement) {
        super(container);

        this._labelContainer = this._container.querySelector<HTMLElement>('.ids-expander__label');
        this._hasLabel = this._container.classList.contains('ids-expander--has-label');

        if (this._hasLabel) {
            this._collapseLabel = this._container.dataset.collapseLabel;
            this._expandLabel = this._container.dataset.expandLabel;
        }
    }

    set expandHandler(value: ExpandHandlerType) {
        this._expandHandler = value;
    }

    isExpanded(): boolean {
        return this._container.classList.contains('ids-expander--is-expanded');
    }

    toggleIsExpanded(isExpanded: boolean) {
        this._container.classList.toggle('ids-expander--is-expanded', isExpanded);

        if (this._hasLabel && this._collapseLabel && this._expandLabel && this._labelContainer) {
            this._labelContainer.innerHTML = isExpanded ? this._collapseLabel : this._expandLabel;
        }
    }

    init() {
        this._container.addEventListener('click', () => {
            this._expandHandler?.(!this.isExpanded());
        });

        super.init();
    }
}
