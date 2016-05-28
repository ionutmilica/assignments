package assignment1;

import java.awt.*;
import java.awt.image.BufferedImage;

public class BlendFilter implements IFilter {

    private BufferedImage over;
    private double ratio;

    public BlendFilter(BufferedImage over, double ratio) {
        this.over = over;
        this.ratio = ratio;
    }

    public BufferedImage apply(BufferedImage source) {
        BufferedImage dest = new BufferedImage(source.getWidth(), source.getHeight(), BufferedImage.TYPE_INT_RGB);

        int w = source.getWidth();
        int h = source.getHeight();

        for (int y = 0; y < h; y++) {
            for (int x = 0; x < w; x++) {
                Color3 first = new Color3(source.getRGB(x, y));
                Color3 last = new Color3(over.getRGB(x, y));

                float fact = (float) ratio;
                float r = fact * first.getRed() +  (1 - fact) * last.getRed();
                float g = fact * first.getGreen() +  (1 - fact) * last.getGreen();
                float b = fact * first.getBlue() +  (1 - fact) * last.getBlue();

                int red = r > 255 ? 255 : (int) r;
                int green = g > 255 ? 255 : (int) g;
                int blue = b > 255 ? 255 : (int) b;


                dest.setRGB(x, y, (new Color(red, green, blue, 255)).getRGB());
            }
        }

        return dest;
    }
}
