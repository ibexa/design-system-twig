import Base from '../shared/Base';

type ExpandHandlerType = (isExpanded: boolean) => void;

export default class Expander extends Base {
    private _expandHandler: ExpandHandlerType | undefined;
    private _hasLabel: boolean;
    private _collapseLabel: string | undefined;
    private _expandLabel: string | undefined;

    constructor(container: HTMLElement) {
        super(container);

        this._hasLabel = this.container.classList.contains('ibexa-expander--has-label');

        if (this._hasLabel) {
            this._collapseLabel = container.dataset.collapseLabel;
            this._expandLabel = container.dataset.expandLabel;
        }
    }

    set expandHandler(value: ExpandHandlerType) {
        this._expandHandler = value;
    }

    isExpanded(): boolean {
        return this.container.classList.contains('ibexa-expander--is-expanded');
    }

    toggleIsExpanded(isExpanded: boolean) {
        this.container.classList.toggle('ibexa-expander--is-expanded', isExpanded);

        if (this._hasLabel) {
            this.container.innerHTML = (isExpanded ? this._collapseLabel : this._expandLabel) ?? '';
        }
    }

    init() {
        this.container.addEventListener('click', () => {
            if (typeof this._expandHandler !== 'function') {
                throw new Error('No expandHandler method provided!');
            }

            this._expandHandler(!this.isExpanded());
        });

        super.init();
    }
}

export type ExpanderType = InstanceType<typeof Expander>;

const expanderContainers = document.querySelectorAll<HTMLElement>('.ibexa-expander:not([custom-init])');

expanderContainers.forEach((expanderContainer: HTMLElement) => {
    const expanderInstance = new Expander(expanderContainer);

    expanderInstance.init();
});
