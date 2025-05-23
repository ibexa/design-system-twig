import { setInstance } from '../helpers/object.instances';

export default abstract class Base {
    container: HTMLElement;

    constructor(container: HTMLElement) {
        this.container = container;

        setInstance(container, this);
    }

    init() {
        this.container.dispatchEvent(new CustomEvent('ibexa-ds:component:initialized', { detail: { component: this } }));
    }
}
