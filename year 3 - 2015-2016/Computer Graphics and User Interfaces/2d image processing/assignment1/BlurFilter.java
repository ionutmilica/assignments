package assignment1;

import javafx.scene.image.PixelReader;
import javafx.scene.paint.Color;

import java.awt.image.BufferedImage;

public class BlurFilter implements IFilter {

    private int level;

    public BlurFilter(int level) {
        this.level = level;
    }

    protected Color3[][] transform(BufferedImage img) {
        Color3[][] newImg = new Color3[img.getWidth()][img.getHeight()];

        for (int y = 0; y < img.getHeight(); y++) {
            for (int x = 0; x < img.getWidth(); x++) {
                newImg[x][y] = new Color3(img.getRGB(x, y));
            }
        }

        return newImg;
    }

    public BufferedImage apply(BufferedImage source) {
        BufferedImage dest = new BufferedImage(source.getWidth(), source.getHeight(), BufferedImage.TYPE_INT_RGB);
        int kernelSize = this.level;

        Color3[][] newImg = transform(source);

        int w = source.getWidth();
        int h = source.getHeight();

        for (int y = 0; y < h; y++) {
            for (int x = 0; x < w; x++) {
                Color3 color =  new Color3(0, 0, 0, 0);
                double red = 0;
                double green = 0;
                double blue = 0;
                double alpha = 0;
                int count = 0;

                for (int i = -kernelSize; i <= kernelSize; i++) {
                    for (int j = -kernelSize; j <= kernelSize; j++) {
                        if (x + i < 0 || x + i >= w || y + j < 0 || y + j >= h) {
                            continue;
                        }
                        Color3 col = newImg[x + i][y + j];
                        red += col.getRed();
                        green += col.getGreen();
                        blue += col.getBlue();
                        alpha += col.getAlpha();
                        count++;
                    }
                }
                Color3 blurColor = new Color3((int) (red / count),
                        (int) (green / count),
                        (int) (blue / count),
                        (int) (alpha / count));
                dest.setRGB(x, y, blurColor.getRGB());
            }
        }

        return dest;
    }
}
