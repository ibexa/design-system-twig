import { Base } from '../partials';

const NAVIGATION_FORWARD_KEYS = ['ArrowRight', 'ArrowDown'];
const NAVIGATION_BACKWARD_KEYS = ['ArrowLeft', 'ArrowUp'];
const RESIZE_TIMEOUT = 200;

const ITEM_SELECTOR = '.ids-switcher__item:not(.ids-switcher__item--more)';
const RADIO_SELECTOR = '.ids-switcher__item[role="radio"]';

export class Switcher extends Base {
    private _isOverflow: boolean;
    private _moreButton: HTMLButtonElement | null;
    private _menu: HTMLDivElement | null = null;
    private _resizeTimeoutId: number | null = null;
    private _resizeObserver: ResizeObserver | null = null;

    constructor(container: HTMLElement) {
        super(container);

        this._isOverflow = container.classList.contains('ids-switcher--overflow');
        this._moreButton = container.querySelector<HTMLButtonElement>('.ids-switcher__item--more');

        this.onClick = this.onClick.bind(this);
        this.onKeyDown = this.onKeyDown.bind(this);
        this.onDocumentMouseDown = this.onDocumentMouseDown.bind(this);
    }

    private getTrackItems(): HTMLButtonElement[] {
        return Array.from(this._container.querySelectorAll<HTMLButtonElement>(`:scope > ${ITEM_SELECTOR}`));
    }

    private getEnabledTrackItems(): HTMLButtonElement[] {
        return this.getTrackItems().filter((item) => !item.disabled && !item.hasAttribute('hidden'));
    }

    private selectValue(value: string) {
        const radioButtons = this._container.querySelectorAll<HTMLButtonElement>(RADIO_SELECTOR);

        radioButtons.forEach((button) => {
            const isSelected = button.dataset.value === value;
            const isTrackItem = button.parentElement === this._container;

            button.classList.toggle('ids-switcher__item--selected', isSelected);
            button.setAttribute('aria-checked', isSelected ? 'true' : 'false');
            button.setAttribute('tabindex', isSelected && isTrackItem ? '0' : '-1');
        });

        this._container.dispatchEvent(new CustomEvent('ids:switcher:change', { bubbles: true, detail: { value } }));
    }

    private onClick(event: MouseEvent) {
        const { target } = event;
        const button = target instanceof Element ? target.closest<HTMLButtonElement>(ITEM_SELECTOR) : null;

        if (!button || button.disabled) {
            return;
        }

        // Menu items live outside the track; picking one promotes it into the visible track.
        if (this._menu?.contains(button)) {
            this.promoteAndSelect(button.dataset.value ?? '');
            this.closeMenu();

            return;
        }

        if (button.parentElement === this._container && button.getAttribute('aria-checked') !== 'true') {
            this.selectValue(button.dataset.value ?? '');
        }
    }

    private onKeyDown(event: KeyboardEvent) {
        const isForward = NAVIGATION_FORWARD_KEYS.includes(event.key);
        const isBackward = NAVIGATION_BACKWARD_KEYS.includes(event.key);

        if (!isForward && !isBackward) {
            return;
        }

        const { target } = event;
        const enabledItems = this.getEnabledTrackItems();
        const currentIndex = target instanceof HTMLElement ? enabledItems.indexOf(target as HTMLButtonElement) : -1; // eslint-disable-line @typescript-eslint/no-unsafe-type-assertion

        if (currentIndex === -1 || enabledItems.length === 0) {
            return;
        }

        event.preventDefault();

        const step = isForward ? 1 : -1; // eslint-disable-line no-magic-numbers
        const nextIndex = (currentIndex + step + enabledItems.length) % enabledItems.length;
        const nextItem = enabledItems[nextIndex];

        this.selectValue(nextItem.dataset.value ?? '');
        nextItem.focus();
    }

    private onDocumentMouseDown(event: MouseEvent) {
        const { target } = event;

        if (target instanceof Node && !this._container.contains(target)) {
            this.closeMenu();
        }
    }

    private recalculate() {
        if (!this._moreButton) {
            return;
        }

        const items = this.getTrackItems();

        items.forEach((item) => {
            item.removeAttribute('hidden');
        });
        this._moreButton.removeAttribute('hidden');

        const fits = () => this._container.scrollWidth <= this._container.clientWidth;

        // Hide trailing items (last-first) until the visible items plus the More trigger fit.
        [...items].reverse().forEach((item) => {
            if (!fits()) {
                item.setAttribute('hidden', 'true');
            }
        });

        const hasHidden = items.some((item) => item.hasAttribute('hidden'));

        if (!hasHidden) {
            this._moreButton.setAttribute('hidden', 'true');
            this.closeMenu();
        }
    }

    private buildMenu() {
        const hiddenItems = this.getTrackItems().filter((item) => item.hasAttribute('hidden'));
        const menu = document.createElement('div');

        menu.className = 'ids-switcher__menu';
        menu.setAttribute('role', 'menu');

        hiddenItems.forEach((source) => {
            const clone = source.cloneNode(true) as HTMLButtonElement; // eslint-disable-line @typescript-eslint/no-unsafe-type-assertion

            clone.removeAttribute('hidden');
            clone.setAttribute('tabindex', '-1');
            menu.appendChild(clone);
        });

        this._container.appendChild(menu);
        this._menu = menu;
    }

    private toggleMenu() {
        if (this._menu) {
            this.closeMenu();

            return;
        }

        this.buildMenu();
        this._moreButton?.setAttribute('aria-expanded', 'true');
    }

    private closeMenu() {
        this._menu?.remove();
        this._menu = null;
        this._moreButton?.setAttribute('aria-expanded', 'false');
    }

    private promoteAndSelect(value: string) {
        const item = this.getTrackItems().find((trackItem) => trackItem.dataset.value === value);

        if (item) {
            const [firstItem] = this.getTrackItems();

            this._container.insertBefore(item, firstItem);
            item.removeAttribute('hidden');
        }

        this.selectValue(value);
        this.recalculate();
    }

    private onMoreClick(event: MouseEvent) {
        event.stopPropagation();
        this.toggleMenu();
    }

    public init() {
        this._container.addEventListener('click', this.onClick);
        this._container.addEventListener('keydown', this.onKeyDown);

        if (this._isOverflow && this._moreButton) {
            this._moreButton.addEventListener('click', this.onMoreClick.bind(this));
            document.addEventListener('mousedown', this.onDocumentMouseDown);

            this._resizeObserver = new ResizeObserver(() => {
                if (this._resizeTimeoutId) {
                    clearTimeout(this._resizeTimeoutId);
                }

                this._resizeTimeoutId = window.setTimeout(() => {
                    this.closeMenu();
                    this.recalculate();
                }, RESIZE_TIMEOUT);
            });
            this._resizeObserver.observe(this._container);

            this.recalculate();
        }

        super.init();
    }
}
