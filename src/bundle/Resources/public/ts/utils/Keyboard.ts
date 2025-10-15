export interface KeyboardListenersMapKeyType {
    key: string;
    target: HTMLElement | Window;
}

export class Keyboard {
    private _listenersSignalsMap = new Map<KeyboardListenersMapKeyType, (event: KeyboardEvent) => void>();

    public getKey(key: string, target: HTMLElement | Window | null) {
        return { key, target: target ?? window };
    }

    public bindKey(keys: string[], callback: (event: KeyboardEvent) => void, node: HTMLElement | null) {
        const listenerElement = node ?? window;

        keys.forEach((key) => {
            const listenerKey = this.getKey(key, listenerElement);
            const handleKeyDown = (event: Event) => {
                if (event instanceof KeyboardEvent && key.includes(event.key)) {
                    callback(event);
                }
            };

            listenerElement.addEventListener('keydown', handleKeyDown);
            this._listenersSignalsMap.set(listenerKey, handleKeyDown);
        });
    }
}
