import { ModifierArguments, Options, createPopper } from '@popperjs/core';

import { Base } from '../base';
import { Expander } from '../../components/expander';
import { InputTextInput } from '../../components/input_text';
import { Keyboard } from '../../utils/Keyboard';

import { getBetterFittingPlacementHeight, getBottomAndTopAvailableSpace, getItemsHeight } from './utils';
import { HTMLElementIDSInstance } from '../../shared/types';

export interface BaseDropdownItem {
    id: string;
    label: string;
}
interface TemplatesType {
    item?: HTMLTemplateElement;
}

const MAX_VISIBLE_ITEMS_DEFAULT = 10;
const POPPER_OFFSET = 4;

/* eslint-disable max-lines */
export abstract class BaseDropdown extends Base {
    protected _expanderInstance: Expander;
    protected _searchInstance: InputTextInput;
    protected _itemsContainerNode: HTMLDivElement;
    protected _itemsNode: HTMLUListElement;
    protected _placeholderNode: HTMLDivElement;
    protected _searchNode: HTMLDivElement;
    protected _searchWidgetNode: HTMLDivElement;
    protected _selectionInfoNode: HTMLDivElement;
    protected _selectionInfoItemsNode: HTMLDivElement;
    protected _sourceNode: HTMLDivElement;
    protected _widgetNode: HTMLDivElement;
    protected _templates: TemplatesType = {};
    protected _itemsMap = new Map<string, BaseDropdownItem>();
    protected _isExpanded = false;
    protected _keyboard = new Keyboard();
    protected _itemsContainerPopperInstance: ReturnType<typeof createPopper> | null = null;
    private _itemsNodeOriginalHeight = 0;

    /* eslint-disable-next-line max-lines-per-function */
    constructor(container: HTMLDivElement) {
        super(container);

        const togglerNode = this._container.querySelector<HTMLElementIDSInstance<Expander>>('.ids-expander');
        const itemsContainerNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__items-container');
        const itemsNode = itemsContainerNode?.querySelector<HTMLUListElement>('.ids-dropdown__items');
        const selectionInfoNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__selection-info');
        const placeholderNode = selectionInfoNode?.querySelector<HTMLDivElement>('.ids-dropdown__placeholder');
        const searchNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__search');
        const searchWidgetNode = searchNode?.querySelector<HTMLDivElement>('.ids-input-text');
        const selectionInfoItemsNode = selectionInfoNode?.querySelector<HTMLDivElement>('.ids-dropdown__selection-info-items');
        const sourceNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__source');
        const widgetNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__widget');

        if (
            !togglerNode ||
            !itemsContainerNode ||
            !itemsNode ||
            !placeholderNode ||
            !searchNode ||
            !searchWidgetNode ||
            !selectionInfoItemsNode ||
            !selectionInfoNode ||
            !sourceNode ||
            !widgetNode
        ) {
            throw new Error('Dropdown: Required elements are missing in the container.');
        }

        this._expanderInstance = new Expander(togglerNode);
        this._searchInstance = new InputTextInput(searchWidgetNode);
        this._itemsContainerNode = itemsContainerNode;
        this._itemsNode = itemsNode;
        this._placeholderNode = placeholderNode;
        this._searchNode = searchNode;
        this._searchWidgetNode = searchWidgetNode;
        this._selectionInfoNode = selectionInfoNode;
        this._selectionInfoItemsNode = selectionInfoItemsNode;
        this._sourceNode = sourceNode;
        this._widgetNode = widgetNode;

        this._templates = {
            item: this._container.querySelector<HTMLTemplateElement>('template.ids-dropdown__template[data-id="item"]') ?? undefined,
        };

        const itemsNodes = this.getItemsNodes();

        this.setItemsMapFromNodes(itemsNodes);

        this.toggleItemsContainer = this.toggleItemsContainer.bind(this);
    }

    /******* DOM management ********/

    protected setItemsContainer() {
        const template = this._templates.item?.content.querySelector<HTMLLIElement>('li');

        if (!template) {
            throw new Error('DropdownSingleInput: Item template is missing in the container.');
        }

        this._itemsNode.innerHTML = '';

        this._itemsMap.forEach((item) => {
            const listItem = template.cloneNode(true);

            if (!(listItem instanceof HTMLLIElement)) {
                return;
            }

            listItem.dataset.id = item.id;
            listItem.dataset.label = item.label;

            const itemContent = this.getItemContent(item, listItem);

            if (itemContent instanceof NodeList) {
                listItem.innerHTML = '';
                Array.from(itemContent).forEach((childNode) => {
                    listItem.appendChild(childNode);
                });
            } else {
                listItem.textContent = itemContent;
            }

            this._itemsNode.appendChild(listItem);
        });

        this.initItems();
    }

    protected toggleSearchVisibility(): void {
        const { maxVisibleItems: maxVisibleItemsString } = this._itemsNode.dataset;
        const maxVisibleItems = maxVisibleItemsString ? parseInt(maxVisibleItemsString, 10) : MAX_VISIBLE_ITEMS_DEFAULT;
        const shouldBeVisible = this._itemsMap.size >= maxVisibleItems;

        if (shouldBeVisible) {
            this._searchNode.removeAttribute('hidden');
        } else {
            this._searchNode.setAttribute('hidden', '');
        }
    }

    protected abstract setSource(): void;

    /******* Items management ********/

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    public getItemContent(item: BaseDropdownItem, _listItem: HTMLLIElement): NodeListOf<ChildNode> | string {
        return item.label;
    }

    public getItemsNodes() {
        return [...this._itemsNode.querySelectorAll<HTMLLIElement>('.ids-dropdown__item')];
    }

    public getItemFromNode(itemNode: HTMLLIElement): BaseDropdownItem | undefined {
        const { id, label } = itemNode.dataset;

        if (!id || !label) {
            return;
        }

        return { id, label };
    }

    public getItemById(id = ''): BaseDropdownItem | undefined {
        return this._itemsMap.get(id);
    }

    protected setItemsMapFromNodes(itemsNodes: HTMLLIElement[]) {
        this._itemsMap.clear();

        itemsNodes.forEach((itemNode) => {
            const item = this.getItemFromNode(itemNode);

            if (item) {
                this._itemsMap.set(item.id, item);
            }
        });
    }

    protected setItemsMapFromItems(items: BaseDropdownItem[]) {
        this._itemsMap.clear();

        items.forEach((item) => {
            this._itemsMap.set(item.id, item);
        });
    }

    public setItems(items: BaseDropdownItem[]) {
        this.setItemsMapFromItems(items);
        this.setItemsContainer();
        this.setSource();
        this.toggleSearchVisibility();
    }

    public abstract setValue(value: string): void;

    /******* Dropdown view management ********/

    public isSearchVisible(): boolean {
        return !this._searchNode.hasAttribute('hidden');
    }

    protected clickOutsideItemsContainerHandler = (event: MouseEvent) => {
        if (event.target instanceof Node && !this._widgetNode.contains(event.target) && !this._itemsContainerNode.contains(event.target)) {
            this.toggleItemsContainer(false);
        }
    };

    protected filterFunction(item: BaseDropdownItem, query: string): boolean {
        return item.label.toLowerCase().includes(query.toLowerCase());
    }

    public searchItems(query: string): void {
        const itemsNodes = this.getItemsNodes();

        itemsNodes.forEach((itemNode) => {
            const item = this.getItemById(itemNode.dataset.id ?? '');
            const isVisible = item ? this.filterFunction(item, query) : false;

            if (isVisible) {
                itemNode.removeAttribute('hidden');
            } else {
                itemNode.setAttribute('hidden', '');
            }
        });
    }

    private calculateItemsNodeHeight(): number {
        const availableSpace = getBottomAndTopAvailableSpace(this._widgetNode);
        const availableHeight = getBetterFittingPlacementHeight(this._itemsNodeOriginalHeight, availableSpace);
        const nextHeight = getItemsHeight(availableHeight, {
            itemsContainer: this._itemsContainerNode,
            itemsList: this._itemsNode,
            popperOffset: POPPER_OFFSET,
        });

        return nextHeight;
    }

    public toggleItemsContainer(nextIsExpanded?: boolean) {
        const isExpanded = nextIsExpanded ?? !this._isExpanded;

        this._expanderInstance.toggleIsExpanded(isExpanded);
        this._isExpanded = isExpanded;

        if (isExpanded) {
            const searchInput = this._searchInstance.getInputElement();

            this._itemsContainerNode.style.width = `${this._widgetNode.offsetWidth.toString()}px`;

            this._itemsContainerNode.removeAttribute('hidden');
            this._itemsContainerNode.style.setProperty('visibility', 'hidden');
            this._itemsNodeOriginalHeight = this._itemsContainerNode.offsetHeight;

            const nextHeight = this.calculateItemsNodeHeight();

            this._itemsNode.style.height = `${nextHeight}px`;
            this._itemsContainerNode.style.removeProperty('visibility');
            searchInput.focus();
            document.addEventListener('click', this.clickOutsideItemsContainerHandler);
            void this._itemsContainerPopperInstance?.update();
        } else {
            this._itemsContainerNode.setAttribute('hidden', '');
            this._searchInstance.changeValue('');
            document.removeEventListener('click', this.clickOutsideItemsContainerHandler);
        }
    }

    public abstract onItemClick(event: MouseEvent): void;

    /******* Initializers ********/

    protected initExpander() {
        this._expanderInstance.init();

        this._widgetNode.addEventListener('click', () => {
            this.toggleItemsContainer(!this._isExpanded);
        });
    }

    protected getRecalculateHeightModifier() {
        return {
            enabled: true,
            fn: ({ state }: ModifierArguments<Options>) => {
                const isHidden = state.elements.popper.hasAttribute('hidden');

                if (isHidden) {
                    return;
                }

                const nextHeight = this.calculateItemsNodeHeight();

                this._itemsNode.style.height = `${nextHeight}px`;
            },
            name: 'recalculateHeight',
            phase: 'write' as const,
        };
    }

    protected getHideOutsideViewportModifier() {
        return {
            enabled: true,
            fn: ({ state }: ModifierArguments<Options>) => {
                const isHidden = state.elements.popper.hasAttribute('hidden');

                if (isHidden) {
                    return;
                }

                const { top: referenceTop, bottom: referenceBottom } = this._widgetNode.getBoundingClientRect();
                const { innerHeight: windowHeight } = window;

                if (referenceBottom < 0 || referenceTop > windowHeight) {
                    this.toggleItemsContainer(false);
                }
            },
            name: 'hideOutsideViewport',
            phase: 'main' as const,
        };
    }

    protected initItemsContainer() {
        const popperConfig = {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, POPPER_OFFSET],
                    },
                },
                this.getRecalculateHeightModifier(),
                this.getHideOutsideViewportModifier(),
            ],
            placement: 'bottom-start' as const,
            strategy: 'fixed' as const,
        };

        this._itemsContainerPopperInstance = createPopper(this._widgetNode, this._itemsContainerNode, popperConfig);
    }

    protected initItems() {
        const items = this.getItemsNodes();

        items.forEach((item) => {
            item.addEventListener('click', this.onItemClick);
        });
    }

    protected initSearch() {
        this._searchInstance.init();

        const searchInput = this._searchInstance.getInputElement();

        searchInput.addEventListener('input', () => {
            this.searchItems(searchInput.value);
        });
    }

    protected initKeyboardWidgetOpenEvent() {
        this._keyboard.bindKey(
            ['Enter', ' '],
            (event) => {
                event.preventDefault();
                this.toggleItemsContainer();
            },
            this._widgetNode,
        );
    }

    protected initKeyboardDropdownSelectEvent() {
        this._keyboard.bindKey(
            ['Enter', ' '],
            (event) => {
                const { activeElement } = window.document;

                if (this._isExpanded && activeElement?.classList.contains('ids-dropdown__item') && activeElement instanceof HTMLElement) {
                    event.preventDefault();
                    activeElement.click();
                }
            },
            this._itemsContainerNode,
        );
    }

    protected initKeyboardDropdownCloseEvent() {
        this._keyboard.bindKey(
            ['Escape'],
            (event) => {
                if (this._isExpanded) {
                    event.preventDefault();
                    this.toggleItemsContainer(false);
                    this._widgetNode.focus();
                }
            },
            this._itemsContainerNode,
        );
    }

    protected initKeyboardDropdownMoveEvents() {
        this._keyboard.bindKey(
            ['ArrowDown'],
            (event) => {
                const { activeElement } = window.document;

                if (this._isExpanded && activeElement?.classList.contains('ids-dropdown__item')) {
                    const nextElement = activeElement.nextElementSibling;

                    if (nextElement?.classList.contains('ids-dropdown__item') && nextElement instanceof HTMLElement) {
                        event.preventDefault();
                        nextElement.focus();
                    }
                }
            },
            this._itemsContainerNode,
        );

        this._keyboard.bindKey(
            ['ArrowUp'],
            (event) => {
                const { activeElement } = window.document;

                if (this._isExpanded && activeElement?.classList.contains('ids-dropdown__item')) {
                    const prevElement = activeElement.previousElementSibling;

                    if (prevElement?.classList.contains('ids-dropdown__item') && prevElement instanceof HTMLElement) {
                        event.preventDefault();
                        prevElement.focus();
                    }
                }
            },
            this._itemsContainerNode,
        );
    }

    protected initKeyboard() {
        this.initKeyboardWidgetOpenEvent();
        this.initKeyboardDropdownSelectEvent();
        this.initKeyboardDropdownCloseEvent();
        this.initKeyboardDropdownMoveEvents();
    }

    public init() {
        this.initExpander();
        this.initItemsContainer();
        this.initItems();
        this.initSearch();
        this.initKeyboard();

        super.init();
    }
}
