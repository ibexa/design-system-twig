import { Base } from '../partials';

interface ChipConfig {
    onClose?: (event: Event) => void;
}

export default class Chip extends Base {
    private closeButton: HTMLButtonElement | null;
    private onClose?: (event: Event) => void;

    constructor(container: HTMLDivElement, config: ChipConfig = {}) {
        super(container);

        this.closeButton = this._container.querySelector('.ids-chip__close');
        this.onClose = config.onClose;
    }

    private handleClose(event: MouseEvent): void {
        event.stopPropagation();
        if (this.onClose) {
            this.onClose(event);
        }
    }

    private initCloseButton(closeButton: HTMLButtonElement): void {
        closeButton.addEventListener('click', this.handleClose.bind(this));
    }

    public init(): void {
        if (this.closeButton) {
            this.initCloseButton(this.closeButton);
        }
        super.init();
    }
}
