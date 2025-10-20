import { Base } from '../partials';
import { escapeHTML } from '@ids-core/helpers/escape';

export class OverflowList extends Base {
    private _moreItemNode: HTMLDivElement;
    private _numberOfItems = 0;
    private _numberOfVisibleItems = 0;
    private _templates: Record<'item' | 'itemMore', string> = {
        item: '',
        itemMore: '',
    };

    private _resizeObserver = new ResizeObserver(() => {
        this.resetState();
        this.rerender();
    });

    constructor(container: HTMLDivElement) {
        super(container);

        this._templates = {
            item: this.getTemplate('item'),
            itemMore: this.getTemplate('item_more'),
        };

        this.removeTemplate('item');
        this.removeTemplate('item_more');

        const moreItemNode = container.querySelector<HTMLDivElement>(':scope *:last-child');

        if (!moreItemNode) {
            throw new Error('OverflowList: OverflowList elements are missing in the container.');
        }

        this._moreItemNode = moreItemNode;

        this._numberOfItems = this.getItems(false, false).length;
        this._numberOfVisibleItems = this._numberOfItems;
    }

    private getItems(getOnlyVisible = false, withOverflow = true): HTMLDivElement[] {
        const itemsSelector = `:scope > *${getOnlyVisible ? ':not([hidden])' : ''}`;
        const items = Array.from(this._container.querySelectorAll<HTMLDivElement>(itemsSelector));

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

    private removeTemplate(type: 'item' | 'item_more') {
        const templateNode = this._container.querySelector<HTMLTemplateElement>(`.ids-overflow-list__template[data-id="${type}"]`);

        templateNode?.remove();
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
        const { right: listRightPosition } = this._container.getBoundingClientRect();
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

        this._container.innerHTML = '';
        this._container.appendChild(fragment);
        this._numberOfItems = items.length;
    }

    public setItems(items: Record<string, string>[]) {
        this.setItemsContainer(items);
        this.resetState();
        this.rerender();
    }

    public init() {
        super.init();

        this.initResizeListener();

        this.rerender();
    }
}
