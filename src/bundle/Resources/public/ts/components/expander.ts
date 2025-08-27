import Base from '../shared/Base';

type ExpandHandlerType = (isExpanded: boolean) => void;

export default class Expander extends Base {
    private _expandHandler: ExpandHandlerType | undefined;
    private _hasLabel: boolean;
    private _collapseLabel: string | undefined;
    private _expandLabel: string | undefined;
    private _labelContainer: HTMLElement;

    constructor(container: HTMLElement) {
        super(container);

        const labelContainer = container.querySelector<HTMLElement>('.ids-expander__label');

        if (!labelContainer) {
            throw new Error('No label container found for this expander!');
        }

        this._labelContainer = labelContainer;
        this._hasLabel = container.classList.contains('ids-expander--has-label');

        if (this._hasLabel) {
            this._collapseLabel = container.dataset.collapseLabel;
            this._expandLabel = container.dataset.expandLabel;
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

        if (this._hasLabel && this._collapseLabel && this._expandLabel) {
            this._labelContainer.innerHTML = isExpanded ? this._collapseLabel : this._expandLabel;
        }
    }

    init() {
        this._container.addEventListener('click', () => {
            if (typeof this._expandHandler !== 'function') {
                throw new Error('No expandHandler method provided!');
            }

            this._expandHandler(!this.isExpanded());
        });

        super.init();
    }
}
