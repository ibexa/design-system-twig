import { Base } from '../partials';

const EVENT_DISMISS_BEFORE = 'ids:alert:dismiss:before';
const EVENT_DISMISSED = 'ids:alert:dismissed';

export class Alert extends Base {
    private closeBtn: HTMLButtonElement | null;

    constructor(container: HTMLDivElement) {
        super(container);

        this.closeBtn = this._container.querySelector('.ids-alert__close-btn');
    }

    protected handleCloseClick(event: MouseEvent): void {
        event.stopPropagation();

        this.dismiss();
    }

    public dismiss(): void {
        const dismissBeforeEvent = new CustomEvent(EVENT_DISMISS_BEFORE, {
            cancelable: true,
            detail: {
                component: this,
            },
        });

        this._container.dispatchEvent(dismissBeforeEvent);

        if (dismissBeforeEvent.defaultPrevented) {
            return;
        }

        this._container.remove();
        this._container.dispatchEvent(
            new CustomEvent(EVENT_DISMISSED, {
                detail: {
                    component: this,
                },
            }),
        );
    }

    protected initCloseBtn(): void {
        this.closeBtn?.addEventListener('click', this.handleCloseClick.bind(this));
    }

    public init(): void {
        super.init();
        this.initCloseBtn();
    }
}
