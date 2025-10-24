export const getBottomAndTopAvailableSpace = (referenceElement: HTMLDivElement): { bottom: number; top: number } => {
    const { innerHeight: windowHeight } = window;
    const { top: referenceTop, bottom: referenceBottom } = referenceElement.getBoundingClientRect();

    return {
        bottom: windowHeight - referenceBottom,
        top: referenceTop,
    };
};

export const getBetterFittingPlacementHeight = (currentHeight: number, { top, bottom }: { top: number; bottom: number }): number => {
    if (bottom < currentHeight && top < currentHeight) {
        return Math.max(top, bottom);
    }

    return currentHeight;
};

export const getItemsHeight = (
    availableHeight: number,
    {
        itemsContainer,
        itemsList,
        popperOffset,
    }: {
        itemsContainer: HTMLDivElement;
        itemsList: HTMLUListElement;
        popperOffset: number;
    },
): number => {
    const EXTRA_VIEWPORT_PADDING = 8;
    const { marginTop: itemsMarginTop, marginBottom: itemsMarginBottom } = window.getComputedStyle(itemsContainer);
    const { top: itemsContainerTop, bottom: itemsContainerBottom } = itemsContainer.getBoundingClientRect();
    const { top: itemsTop, bottom: itemsBottom } = itemsList.getBoundingClientRect();
    const topHeight = parseInt(itemsMarginTop, 10) + (itemsTop - itemsContainerTop);
    const bottomHeight = parseInt(itemsMarginBottom, 10) + (itemsContainerBottom - itemsBottom);
    const calculatedAvailableHeight = availableHeight - topHeight - bottomHeight - popperOffset - EXTRA_VIEWPORT_PADDING;

    return calculatedAvailableHeight;
};
