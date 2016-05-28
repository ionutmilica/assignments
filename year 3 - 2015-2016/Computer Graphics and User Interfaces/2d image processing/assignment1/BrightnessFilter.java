package assignment1;

import java.awt.image.BufferedImage;

public class BrightnessFilter implements IFilter {

    private int level;

    public BrightnessFilter(int level) {
        this.level = level;
    }

    public BufferedImage apply(BufferedImage source) {
        BufferedImage dest = new BufferedImage(source.getWidth(), source.getHeight(), BufferedImage.TYPE_INT_RGB);

        for (int y = 0; y < source.getHeight(); y++) {
            for (int x = 0; x < source.getWidth(); x++) {
                Color3 color = new Color3(source.getRGB(x, y));
                dest.setRGB(x, y, color.add(level).getRGB());
            }
        }

        return dest;
    }
}
