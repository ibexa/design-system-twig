import { Base } from '../partials';
import { escapeHTML } from '@ids-core/helpers/escape';

const RESIZE_TIMEOUT = 200;

export class OverflowList extends Base {
    private _itemsNode: HTMLDivElement;
    private _moreItemNode: HTMLDivElement;
    private _numberOfItems = 0;
    private _numberOfVisibleItems = 0;
    private _resizeTimeoutId: number | null = null;
    private _templates: Record<'item' | 'itemMore', string> = {
        item: '',
        itemMore: '',
    };

    private _resizeObserver = new ResizeObserver(() => {
        if (this._resizeTimeoutId) {
            clearTimeout(this._resizeTimeoutId);
        }

        this._resizeTimeoutId = window.setTimeout(() => {
            this.setItemsContainerWidth();
            this.resetState();
            this.rerender();
        }, RESIZE_TIMEOUT);
    });

    constructor(container: HTMLDivElement) {
        super(container);

        const itemsNode = container.querySelector<HTMLDivElement>('.ids-overflow-list__items');
        const moreItemNode = itemsNode?.querySelector<HTMLDivElement>(':scope *:last-child');

        if (!itemsNode || !moreItemNode) {
            throw new Error('OverflowList: OverflowList elements are missing in the container.');
        }

        this._itemsNode = itemsNode;
        this._moreItemNode = moreItemNode;
        this._templates = {
            item: this.getTemplate('item'),
            itemMore: this.getTemplate('item_more'),
        };
        this._numberOfItems = this.getItems(false, false).length;
        this._numberOfVisibleItems = this._numberOfItems;
    }

    private getItems(getOnlyVisible = false, withOverflow = true): HTMLDivElement[] {
        const items = getOnlyVisible
            ? Array.from(this._itemsNode.querySelectorAll<HTMLDivElement>(':scope > *:not([hidden])'))
            : Array.from(this._itemsNode.querySelectorAll<HTMLDivElement>(':scope > *'));

        if (withOverflow) {
            return items;
        }

        return items.slice(0, -1);
    }

    private getTemplate(type: 'item' | 'item_more'): string {
        const templateNode = this._container.querySelector<HTMLTemplateElement>(`.ids-overflow-list__template[data-id="${type}"]`);

        if (!templateNode) {
            throw new Error(`OverflowList: Template of type "${type}" is missing in the container.`);
        }

        return templateNode.innerHTML.trim();
    }

    private updateMoreItem() {
        const hiddenCount = this._numberOfItems - this._numberOfVisibleItems;

        if (hiddenCount > 0) {
            const tempMoreItem = document.createElement('div');

            tempMoreItem.innerHTML = this._templates.itemMore.replace('{{ hidden_count }}', hiddenCount.toString());

            if (!tempMoreItem.firstElementChild) {
                throw new Error('OverflowList: Error while creating more item element from template.');
            }

            this._moreItemNode.replaceWith(tempMoreItem.firstElementChild);
        } else {
            this._moreItemNode.setAttribute('hidden', 'true');
        }
    }

    private hideOverflowItems() {
        const itemsNodes = this.getItems(true, false);

        itemsNodes.slice(this._numberOfVisibleItems).forEach((itemNode) => {
            itemNode.setAttribute('hidden', 'true');
        });
    }

    private recalculateVisibleItems() {
        const itemsNodes = this.getItems(true);
        const { right: listRightPosition } = this._itemsNode.getBoundingClientRect();
        const newNumberOfVisibleItems = itemsNodes.findIndex((itemNode) => {
            const { right: itemRightPosition } = itemNode.getBoundingClientRect();

            return itemRightPosition > listRightPosition;
        });

        if (newNumberOfVisibleItems === -1 || newNumberOfVisibleItems === this._numberOfItems) {
            return true;
        }

        if (newNumberOfVisibleItems === this._numberOfVisibleItems) {
            this._numberOfVisibleItems = newNumberOfVisibleItems - 1; // eslint-disable-line no-magic-numbers
        } else {
            this._numberOfVisibleItems = newNumberOfVisibleItems;
        }

        return false;
    }

    private initResizeListener() {
        this._resizeObserver.observe(this._container);
    }

    public resetState() {
        this._numberOfVisibleItems = this._numberOfItems;

        const itemsNodes = this.getItems(false);

        itemsNodes.forEach((itemNode) => {
            itemNode.removeAttribute('hidden');
        });
    }

    public rerender() {
        let stopRecalculating = true;

        do {
            stopRecalculating = this.recalculateVisibleItems();

            this.hideOverflowItems();
            this.updateMoreItem();
        } while (!stopRecalculating);
    }

    private setItemsContainer(items: Record<string, string>[]) {
        const fragment = document.createDocumentFragment();

        items.forEach((item) => {
            const filledItem = Object.entries(item).reduce((acc, [key, value]) => {
                const pattern = `{{ ${key} }}`;
                const escapedValue = escapeHTML(value);

                return acc.replaceAll(pattern, escapedValue);
            }, this._templates.item);
            const container = document.createElement('div');

            container.innerHTML = filledItem;

            if (container.firstElementChild) {
                fragment.append(container.firstElementChild);
            }
        });

        // Needs to use type assertion here as cloneNode returns a Node type https://github.com/microsoft/TypeScript/issues/283
        this._moreItemNode = this._moreItemNode.cloneNode(true) as HTMLDivElement; // eslint-disable-line @typescript-eslint/no-unsafe-type-assertion

        fragment.append(this._moreItemNode);

        this._itemsNode.innerHTML = '';
        this._itemsNode.appendChild(fragment);
        this._numberOfItems = items.length;
    }

    private setItemsContainerWidth() {
        const overflowListWidth = this._container.clientWidth;

        this._itemsNode.style.width = `${overflowListWidth}px`;
    }

    public setItems(items: Record<string, string>[]) {
        this.setItemsContainer(items);
        this.resetState();
        this.rerender();
    }

    public init() {
        super.init();

        this.initResizeListener();

        this.setItemsContainerWidth();
        this.rerender();
    }
}
