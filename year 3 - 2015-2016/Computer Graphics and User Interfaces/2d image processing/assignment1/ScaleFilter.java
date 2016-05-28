package assignment1;

import java.awt.image.BufferedImage;

public class ScaleFilter implements IFilter {

    private int factor;

    public ScaleFilter(int factor) {
        this.factor = factor;
    }

    public BufferedImage apply(BufferedImage source) {
        BufferedImage dest = new BufferedImage(source.getWidth() * factor, source.getHeight() * factor, BufferedImage.TYPE_INT_RGB);

        for (int y = 0; y < source.getHeight(); y++) {
            for (int x = 0; x < source.getWidth(); x++) {
                final int argb = source.getRGB(x, y);
                for (int dy = 0; dy < factor; dy++) {
                    for (int dx = 0; dx < factor; dx++) {
                        dest.setRGB(x * factor + dx, y * factor + dy, argb);
                    }
                }
            }
        }

        return dest;
    }
}
