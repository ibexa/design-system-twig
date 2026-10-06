import { Base } from '../partials';

const EVENT_CHANGE_BEFORE = 'ids:tabs:change:before';
const EVENT_CHANGE_AFTER = 'ids:tabs:change:after';
const INDEX_STEP = 1;

export class Tabs extends Base {
    private _listNode: HTMLElement;

    constructor(container: HTMLElement) {
        super(container);

        const listNode = container.querySelector<HTMLElement>('.ids-tabs__list');

        if (!listNode) {
            throw new Error('Tabs: Tabs elements are missing in the container.');
        }

        this._listNode = listNode;
    }

    public getTabs(): HTMLElement[] {
        return Array.from(this._listNode.querySelectorAll<HTMLElement>(':scope > .ids-tabs__item > .ids-tabs__tab'));
    }

    public getSelectedTab(): HTMLElement | null {
        return this.getTabs().find((tab) => tab.getAttribute('aria-selected') === 'true') ?? null;
    }

    public selectTab(tab: HTMLElement): boolean {
        const previousTab = this.getSelectedTab();

        if (tab === previousTab || this.checkIsDisabled(tab)) {
            return false;
        }

        const changeBeforeEvent = new CustomEvent(EVENT_CHANGE_BEFORE, {
            cancelable: true,
            detail: {
                component: this,
                previousTab,
                tab,
            },
        });

        this._container.dispatchEvent(changeBeforeEvent);

        if (changeBeforeEvent.defaultPrevented) {
            return false;
        }

        if (previousTab) {
            this.setTabState(previousTab, false);
        }

        this.setTabState(tab, true);
        this._container.dispatchEvent(
            new CustomEvent(EVENT_CHANGE_AFTER, {
                detail: {
                    component: this,
                    previousTab,
                    tab,
                },
            }),
        );

        return true;
    }

    private checkIsDisabled(tab: HTMLElement): boolean {
        return (
            tab.hasAttribute('disabled') ||
            tab.getAttribute('aria-disabled') === 'true' ||
            tab.classList.contains('ids-tabs__tab--disabled')
        );
    }

    private getPanel(tab: HTMLElement): HTMLElement | null {
        const panelId = tab.getAttribute('aria-controls');

        return panelId ? document.getElementById(panelId) : null;
    }

    private setTabState(tab: HTMLElement, isSelected: boolean): void {
        const panel = this.getPanel(tab);

        tab.classList.toggle('ids-tabs__tab--selected', isSelected);
        tab.setAttribute('aria-selected', String(isSelected));
        tab.setAttribute('tabindex', isSelected ? '0' : '-1');

        if (panel) {
            panel.classList.toggle('ids-tabs__panel--selected', isSelected);
            panel.toggleAttribute('hidden', !isSelected);
        }
    }

    private getNextTab(key: string): HTMLElement | null {
        const enabledTabs = this.getTabs().filter((tab) => !this.checkIsDisabled(tab));

        if (enabledTabs.length === 0) {
            return null;
        }

        const lastIndex = enabledTabs.length - INDEX_STEP;
        const currentIndex = enabledTabs.findIndex((tab) => tab === document.activeElement);
        const nextIndexByKey: Partial<Record<string, number>> = {
            ArrowLeft: currentIndex <= 0 ? lastIndex : currentIndex - INDEX_STEP,
            ArrowRight: currentIndex >= lastIndex ? 0 : currentIndex + INDEX_STEP,
            End: lastIndex,
            Home: 0,
        };
        const nextIndex = nextIndexByKey[key];

        return nextIndex === undefined ? null : enabledTabs[nextIndex];
    }

    private handleTabClick(event: MouseEvent): void {
        const tab = event.target instanceof Element ? event.target.closest<HTMLElement>('.ids-tabs__tab') : null;

        if (!tab || !this._listNode.contains(tab)) {
            return;
        }

        if (tab instanceof HTMLAnchorElement && tab.getAttribute('href')?.startsWith('#')) {
            event.preventDefault();
        }

        this.selectTab(tab);
    }

    private handleKeyDown(event: KeyboardEvent): void {
        const nextTab = this.getNextTab(event.key);

        if (!nextTab) {
            return;
        }

        event.preventDefault();
        nextTab.focus();
        this.selectTab(nextTab);
    }

    public init(): void {
        this._listNode.addEventListener('click', this.handleTabClick.bind(this));
        this._listNode.addEventListener('keydown', this.handleKeyDown.bind(this));

        super.init();
    }
}
