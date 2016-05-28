package assignment1;

import java.awt.image.BufferedImage;

public interface IFilter {

    /**
     * Apply the filter to the algorithm
     *
     * @param source BufferedImage
     */
    public BufferedImage apply(BufferedImage source);
}
