import { BaseDropdownWidgetNodes } from '../../partials';
import { DropdownMultiInput } from './dropdown_multi_input';

const SINGLE_SELECTION_COUNT = 1;
const PANEL_MIN_WIDTH = 200;

export class FilterDropdown extends DropdownMultiInput {
    private _triggerLabelNode: HTMLElement | null;
    private _counterNode: HTMLElement | null;
    private _valueNode: HTMLElement | null;
    private _clearBtnNode: HTMLButtonElement | null;
    private _hasSearch: boolean;

    constructor(container: HTMLDivElement) {
        super(container);

        this._triggerLabelNode = this._widgetNode.querySelector<HTMLElement>('.ids-dropdown__trigger-label');
        this._counterNode = this._widgetNode.querySelector<HTMLElement>('.ids-dropdown__counter');
        this._valueNode = this._widgetNode.querySelector<HTMLElement>('.ids-dropdown__value');
        this._clearBtnNode = this._itemsContainerNode.querySelector<HTMLButtonElement>('.ids-dropdown__clear-btn');
        this._hasSearch = !this._searchNode.hasAttribute('hidden');
    }

    protected resolveWidgetNodes(): BaseDropdownWidgetNodes {
        const widgetNode = this._container.querySelector<HTMLButtonElement>('.ids-dropdown__trigger');

        if (!widgetNode) {
            throw new Error('FilterDropdown: Required elements are missing in the container.');
        }

        return {
            placeholderNode: null,
            selectionInfoItemsNode: null,
            selectionInfoNode: null,
            togglerNode: null,
            widgetNode,
        };
    }

    protected checkIsDashboardType(): boolean {
        return this._container.classList.contains('ids-dropdown--filter-dashboard');
    }

    protected setSelectionInfo(values: string[]) {
        const selectedValues = new Set(values);
        const selectedItems = Array.from(this._itemsMap.values()).filter((item) => selectedValues.has(item.id));
        const label = this._widgetNode.dataset.label ?? '';
        const showsValue = this.checkIsDashboardType() && selectedItems.length === SINGLE_SELECTION_COUNT;
        const showsCounter = selectedItems.length > 0 && !showsValue;

        if (this._triggerLabelNode) {
            this._triggerLabelNode.textContent = showsValue ? `${label}:` : label;
        }

        if (this._valueNode) {
            this._valueNode.textContent = showsValue ? selectedItems[0].label : '';
            this._valueNode.toggleAttribute('hidden', !showsValue);
        }

        if (this._counterNode) {
            this._counterNode.textContent = showsCounter ? selectedItems.length.toString() : '';
            this._counterNode.toggleAttribute('hidden', !showsCounter);
        }

        if (this._clearBtnNode) {
            this._clearBtnNode.disabled = selectedItems.length === 0;
        }

        this._container.classList.toggle('ids-dropdown--selected', selectedItems.length > 0);
    }

    public toggleItemsContainer(nextIsExpanded?: boolean) {
        super.toggleItemsContainer(nextIsExpanded);

        this._widgetNode.setAttribute('aria-expanded', this._isExpanded.toString());
        this._container.classList.toggle('ids-dropdown--open', this._isExpanded);

        if (this._isExpanded) {
            this._itemsContainerNode.style.minWidth = `${Math.max(this._widgetNode.offsetWidth, PANEL_MIN_WIDTH).toString()}px`;
            this.focusFirstFocusableNode();
        }
    }

    protected toggleSearchVisibility(): void {
        this._searchNode.toggleAttribute('hidden', !this._hasSearch);
    }

    protected focusFirstFocusableNode() {
        const searchInput = this._searchInstance.getInputElement();

        if (this._hasSearch) {
            searchInput.focus();

            return;
        }

        this.getVisibleItemsNodes().at(0)?.focus();
    }

    protected initClearBtn() {
        this._clearBtnNode?.addEventListener('click', () => {
            this.clearCurrentSelection();
            this.focusFirstFocusableNode();
        });
    }

    public init() {
        this.initClearBtn();

        super.init();
    }
}
