import assignment1.BrightnessFilter;
import assignment1.Dithering;

import javax.swing.*;
import java.awt.*;

public class TestBrightness extends JFrame {

    public static void main(String[] args) {
        EventQueue.invokeLater(new Runnable() {
            @Override
            public void run() {
                TestBrightness m = new TestBrightness();
                m.setVisible(true);
            }
        });
    }

    protected void initUI() {
        setTitle("Assignment 1");
        setSize(1000, 800);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setLayout(new GridLayout(1, 1));

        assignment1.Image img = new assignment1.Image("1.jpg");

        this.add(new JComponent() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                g.drawImage(img.getOriginal(), 0, 0, this);
            }
        });

        img.apply(new BrightnessFilter(-50));

        this.add(new JComponent() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                g.drawImage(img.getFiltered(), 0, 0, this);
            }
        });

    }

    public TestBrightness() {
        initUI();
    }
}
